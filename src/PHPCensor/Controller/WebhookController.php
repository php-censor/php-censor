<?php

namespace PHPCensor\Controller;

use b8;
use b8\Store;
use Exception;
use GuzzleHttp\Client;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use b8\Controller;
use b8\Config;
use b8\Exception\HttpException\NotFoundException;

/**
 * Webhook Controller - Processes webhook pings from BitBucket, Github, Gitlab, Gogs, etc.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Sami Tikka <stikka@iki.fi>
 * @author Alex Russell <alex@clevercherry.com>
 * @author Guillaume Perréal <adirelle@gmail.com>
 *
 */
class WebhookController extends Controller
{
    /**
     * @var BuildStore
     */
    protected $buildStore;

    /**
     * @var ProjectStore
     */
    protected $projectStore;

    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore = Store\Factory::getStore('Build');
        $this->projectStore = Store\Factory::getStore('Project');
        $this->buildService = new BuildService($this->buildStore);
    }

    /** Handle the action, Ensuring to return a JsonResponse.
     *
     * @param string $action
     * @param mixed $actionParams
     *
     * @return \b8\Http\Response
     */
    public function handleAction($action, $actionParams)
    {
        $response = new b8\Http\Response\JsonResponse();
        try {
            $data = parent::handleAction($action, $actionParams);
            if (isset($data['responseCode'])) {
                $response->setResponseCode($data['responseCode']);
                unset($data['responseCode']);
            }
            $response->setContent($data);
        } catch (Exception $ex) {
            $response->setResponseCode(500);
            $response->setContent(['status' => 'failed', 'error' => $ex->getMessage()]);
        }
        return $response;
    }

    /**
     * Called by Bitbucket.
     */
    public function bitbucket($projectId)
    {
        $project = $this->fetchProject($projectId, ['bitbucket', 'bitbuckethg', 'remote']);

        // Support both old services and new webhooks
        if ($payload = $this->getParam('payload')) {
            return $this->bitbucketService(json_decode($payload, true), $project);
        }

        $payload = json_decode(file_get_contents("php://input"), true);

        // Handle Pull Request webhooks:
        if (!empty($payload['pullrequest'])) {
            return $this->bitbucketPullRequest($project, $payload);
        }

        // Handle Push (and Tag) webhooks:
        if (!empty($payload['push']['changes'])) {
            return $this->bitbucketCommitRequest($project, $payload);
        }

        // Invalid event from bitbucket
        return [
            'status' => 'failed',
            'commits' => []
        ];
    }

    /**
     * Handle the payload when Bitbucket sends a commit webhook.
     *
     * @param Project $project
     * @param array $payload
     *
     * @return array
     */
    protected function bitbucketCommitRequest(Project $project, array $payload)
    {
        $results = [];
        $status  = 'failed';
        foreach ($payload['push']['changes'] as $commit) {
            try {
                $email = $commit['new']['target']['author']['raw'];
                if (strpos($email, '>') !== false) {
                    // In order not to loose email if it is RAW, w/o "<>" symbols
                    $email = substr($email, 0, strpos($email, '>'));
                    $email = substr($email, strpos($email, '<') + 1);
                }

                $results[$commit['new']['target']['hash']] = $this->createBuild(
                    $project,
                    $commit['new']['target']['hash'],
                    $commit['new']['name'],
                    null,
                    $email,
                    $commit['new']['target']['message']
                );
                $status = 'ok';
            } catch (Exception $ex) {
                $results[$commit['new']['target']['hash']] = ['status' => 'failed', 'error' => $ex->getMessage()];
            }
        }

        return ['status' => $status, 'commits' => $results];
    }

    /**
     * Handle the payload when Bitbucket sends a Pull Request webhook.
     *
     * @param Project $project
     * @param array   $payload
     *
     * @return array
     *
     * @throws Exception
     */
    protected function bitbucketPullRequest(Project $project, array $payload)
    {
        // We only want to know about open pull requests:
        if (!in_array($_SERVER['HTTP_X_EVENT_KEY'], ['pullrequest:created', 'pullrequest:updated'])) {
            return ['status' => 'ok'];
        }

        $headers = [];
        $username = Config::getInstance()->get('php-censor.bitbucket.username');
        $appPassword = Config::getInstance()->get('php-censor.bitbucket.app_password');

        if (empty($username) || empty($appPassword)) {
            throw new Exception('Please provide Username and App Password of your Bitbucket account.');
        }

        $commitsUrl = $payload['pullrequest']['links']['commits']['href'];

        $client   = new Client();
        $commitsResponse = $client->get($commitsUrl, [
            'auth' => [$username, $appPassword],
        ]);
        $httpStatus = (integer)$commitsResponse->getStatusCode();

        // Check we got a success response:
        if ($httpStatus < 200 || $httpStatus >= 300) {
            throw new Exception('Could not get commits, failed API request.');
        }

        $results = [];
        $status  = 'failed';
        $commits = json_decode($commitsResponse->getBody(), true)['values'];
        foreach ($commits as $commit) {
            // Skip all but the current HEAD commit ID:
            $id = $commit['hash'];
            if (strpos($id, $payload['pullrequest']['source']['commit']['hash']) !== 0) {
                $results[$id] = ['status' => 'ignored', 'message' => 'not branch head'];
                continue;
            }

            try {
                $branch    = $payload['pullrequest']['destination']['branch']['name'];
                $committer = $commit['author']['raw'];
                if (strpos($committer, '>') !== false) {
                    // In order not to loose email if it is RAW, w/o "<>" symbols
                    $committer = substr($committer, 0, strpos($committer, '>'));
                    $committer = substr($committer, strpos($committer, '<') + 1);
                }
                $message   = $commit['message'];

                $extra = [
                    'build_type'          => 'pull_request',
                    'pull_request_number' => $payload['pullrequest']['id'],
                    'remote_branch'       => $payload['pullrequest']['source']['branch']['name'],
                    'remote_reference'    => $payload['pullrequest']['source']['repository']['full_name'],
                ];

                $results[$id] = $this->createBuild($project, $id, $branch, null, $committer, $message, $extra);
                $status = 'ok';
            } catch (Exception $ex) {
                $results[$id] = ['status' => 'failed', 'error' => $ex->getMessage()];
            }
        }

        return ['status' => $status, 'commits' => $results];
    }

    /**
     * Bitbucket webhooks.
     *
     * @deprecated, for BC purpose
     */
    protected function bitbucketWebhook($payload, $project)
    {
        return $this->bitbucketCommitRequest($project, $payload);
    }

    /**
     * Bitbucket POST service.
     */
    protected function bitbucketService($payload, $project)
    {
        $payload = json_decode($this->getParam('payload'), true);

        $results = [];
        $status  = 'failed';
        foreach ($payload['commits'] as $commit) {
            try {
                $email = $commit['raw_author'];
                $email = substr($email, 0, strpos($email, '>'));
                $email = substr($email, strpos($email, '<') + 1);

                $results[$commit['raw_node']] = $this->createBuild(
                    $project,
                    $commit['raw_node'],
                    $commit['branch'],
                    null,
                    $email,
                    $commit['message']
                );
                $status = 'ok';
            } catch (Exception $ex) {
                $results[$commit['raw_node']] = ['status' => 'failed', 'error' => $ex->getMessage()];
            }
        }

        return ['status' => $status, 'commits' => $results];
    }

    /**
     * Called by POSTing to /webhook/git/<project_id>?branch=<branch>&commit=<commit>
     *
     * @param string $projectId
     *
     * @return array
     */
    public function git($projectId)
    {
        $project = $this->fetchProject($projectId, ['local', 'remote']);
        $branch = $this->getParam('branch', $project->getBranch());
        $commit = $this->getParam('commit');
        $commitMessage = $this->getParam('message');
        $committer = $this->getParam('committer');

        return $this->createBuild($project, $commit, $branch, null, $committer, $commitMessage);
    }

    /**
     * Called by Github Webhooks:
     */
    public function github($projectId)
    {
        $project = $this->fetchProject($projectId, ['github', 'remote']);

        switch ($_SERVER['CONTENT_TYPE']) {
            case 'application/json':
                $payload = json_decode(file_get_contents('php://input'), true);
                break;
            case 'application/x-www-form-urlencoded':
                $payload = json_decode($this->getParam('payload'), true);
                break;
            default:
                return [
                    'status'       => 'failed',
                    'error'        => 'Content type not supported.',
                    'responseCode' => 401
                ];
        }

        // Handle Pull Request webhooks:
        if (array_key_exists('pull_request', $payload)) {
            return $this->githubPullRequest($project, $payload);
        }

        // Handle Push (and Tag) webhooks:
        if (array_key_exists('head_commit', $payload)) {
            return $this->githubCommitRequest($project, $payload);
        }

        return ['status' => 'ignored', 'message' => 'Unusable payload.'];
    }

    /**
     * Handle the payload when Github sends a commit webhook.
     *
     * @param Project $project
     * @param array $payload
     *
     * @return array
     */
    protected function githubCommitRequest(Project $project, array $payload)
    {
        // Github sends a payload when you close a pull request with a non-existent commit. We don't want this.
        if (
            array_key_exists('after', $payload) &&
            $payload['after'] === '0000000000000000000000000000000000000000'
        ) {
            return ['status' => 'ignored'];
        }

        if (isset($payload['head_commit']) && $payload['head_commit']) {
            $isTag   = (substr($payload['ref'], 0, 10) == 'refs/tags/') ? true : false;
            $commit  = $payload['head_commit'];
            $results = [];
            $status  = 'failed';
            
            if (!$commit['distinct']) {
                $results[$commit['id']] = ['status' => 'ignored'];
            } else {
                try {
                    $tag = null;
                    if ($isTag) {
                        $tag       = str_replace('refs/tags/', '', $payload['ref']);
                        $branch    = str_replace('refs/heads/', '', $payload['base_ref']);
                        $committer = $payload['pusher']['email'];
                    } else {
                        $branch    = str_replace('refs/heads/', '', $payload['ref']);
                        $committer = $commit['committer']['email'];
                    }

                    $results[$commit['id']] = $this->createBuild(
                        $project,
                        $commit['id'],
                        $branch,
                        $tag,
                        $committer,
                        $commit['message']
                    );

                    $status = 'ok';
                } catch (Exception $ex) {
                    $results[$commit['id']] = ['status' => 'failed', 'error' => $ex->getMessage()];
                }
            }

            return ['status' => $status, 'commits' => $results];
        }

        return ['status' => 'ignored', 'message' => 'Unusable payload.'];
    }

    /**
     * Handle the payload when Github sends a Pull Request webhook.
     *
     * @param Project $project
     * @param array   $payload
     *
     * @return array
     *
     * @throws Exception
     */
    protected function githubPullRequest(Project $project, array $payload)
    {
        // We only want to know about open pull requests:
        if (!in_array($payload['action'], ['opened', 'synchronize', 'reopened'])) {
            return ['status' => 'ok'];
        }

        $headers = [];
        $token   = Config::getInstance()->get('php-censor.github.token');

        if (!empty($token)) {
            $headers['Authorization'] = 'token ' . $token;
        }

        $url = $payload['pull_request']['commits_url'];

        //for large pull requests, allow grabbing more then the default number of commits
        $custom_per_page = Config::getInstance()->get('php-censor.github.per_page');
        $params          = [];
        if ($custom_per_page) {
            $params['per_page'] = $custom_per_page;
        }

        $client   = new Client();
        $response = $client->get($url, [
            'headers' => $headers,
            'query'   => $params,
        ]);
        $status = (integer)$response->getStatusCode();

        // Check we got a success response:
        if ($status < 200 || $status >= 300) {
            throw new Exception('Could not get commits, failed API request.');
        }

        $results = [];
        $status  = 'failed';
        $commits = json_decode($response->getBody(), true);
        foreach ($commits as $commit) {
            // Skip all but the current HEAD commit ID:
            $id = $commit['sha'];
            if ($id != $payload['pull_request']['head']['sha']) {
                $results[$id] = ['status' => 'ignored', 'message' => 'not branch head'];
                continue;
            }

            try {
                $branch    = str_replace('refs/heads/', '', $payload['pull_request']['base']['ref']);
                $committer = $commit['commit']['author']['email'];
                $message   = $commit['commit']['message'];

                $remoteUrlKey = $payload['pull_request']['head']['repo']['private'] ? 'ssh_url' : 'clone_url';

                $extra = [
                    'build_type'          => 'pull_request',
                    'pull_request_id'     => $payload['pull_request']['id'],
                    'pull_request_number' => $payload['number'],
                    'remote_branch'       => $payload['pull_request']['head']['ref'],
                    'remote_url'          => $payload['pull_request']['head']['repo'][$remoteUrlKey],
                ];

                $results[$id] = $this->createBuild($project, $id, $branch, null, $committer, $message, $extra);
                $status = 'ok';
            } catch (Exception $ex) {
                $results[$id] = ['status' => 'failed', 'error' => $ex->getMessage()];
            }
        }

        return ['status' => $status, 'commits' => $results];
    }

    /**
     * Called by Gitlab Webhooks:
     */
    public function gitlab($projectId)
    {
        $project = $this->fetchProject($projectId, ['gitlab', 'remote']);

        $payloadString = file_get_contents("php://input");
        $payload = json_decode($payloadString, true);

        // build on merge request events
        if (isset($payload['object_kind']) && $payload['object_kind'] == 'merge_request') {
            $attributes = $payload['object_attributes'];
            if ($attributes['state'] == 'opened' || $attributes['state'] == 'reopened') {
                $branch = $attributes['source_branch'];
                $commit = $attributes['last_commit'];
                $committer = $commit['author']['email'];

                return $this->createBuild($project, $commit['id'], $branch, null, $committer, $commit['message']);
            }
        }

        // build on push events
        if (isset($payload['commits']) && is_array($payload['commits'])) {
            // If we have a list of commits, then add them all as builds to be tested:

            $results = [];
            $status  = 'failed';
            foreach ($payload['commits'] as $commit) {
                try {
                    $branch = str_replace('refs/heads/', '', $payload['ref']);
                    $committer = $commit['author']['email'];
                    $results[$commit['id']] = $this->createBuild(
                        $project,
                        $commit['id'],
                        $branch,
                        null,
                        $committer,
                        $commit['message']
                    );
                    $status = 'ok';
                } catch (Exception $ex) {
                    $results[$commit['id']] = ['status' => 'failed', 'error' => $ex->getMessage()];
                }
            }
            return ['status' => $status, 'commits' => $results];
        }

        return ['status' => 'ignored', 'message' => 'Unusable payload.'];
    }


    /**
     * Called by POSTing to /webhook/svn/<project_id>?branch=<branch>&commit=<commit>
     *
     * @author Sylvain Lévesque <slevesque@gezere.com>
     *
     * @param string $projectId
     *
     * @return array
     */
    public function svn($projectId)
    {
        $project = $this->fetchProject($projectId, 'svn');
        $branch = $this->getParam('branch', $project->getBranch());
        $commit = $this->getParam('commit');
        $commitMessage = $this->getParam('message');
        $committer = $this->getParam('committer');

        return $this->createBuild($project, $commit, $branch, null, $committer, $commitMessage);
    }

    /**
     * Called by Gogs Webhooks:
     *
     * @param string $projectId
     *
     * @return array
     */
    public function gogs($projectId)
    {
        $project = $this->fetchProject($projectId, ['gogs', 'remote']);
        switch ($_SERVER['CONTENT_TYPE']) {
            case 'application/json':
                $payload = json_decode(file_get_contents('php://input'), true);
                break;
            case 'application/x-www-form-urlencoded':
                $payload = json_decode($this->getParam('payload'), true);
                break;
            default:
                return ['status' => 'failed', 'error' => 'Content type not supported.', 'responseCode' => 401];
        }

        // Handle Push web hooks:
        if (array_key_exists('commits', $payload)) {
            return $this->gogsCommitRequest($project, $payload);
        }

        return ['status' => 'ignored', 'message' => 'Unusable payload.'];
    }

    /**
     * Handle the payload when Gogs sends a commit webhook.
     *
     * @param Project $project
     * @param array   $payload
     *
     * @return array
     */
    protected function gogsCommitRequest(Project $project, array $payload)
    {
        if (isset($payload['commits']) && is_array($payload['commits'])) {
            // If we have a list of commits, then add them all as builds to be tested:
            $results = [];
            $status  = 'failed';
            foreach ($payload['commits'] as $commit) {
                try {
                    $branch = str_replace('refs/heads/', '', $payload['ref']);
                    $committer = $commit['author']['email'];
                    $results[$commit['id']] = $this->createBuild(
                        $project,
                        $commit['id'],
                        $branch,
                        null,
                        $committer,
                        $commit['message']
                    );
                    $status = 'ok';
                } catch (Exception $ex) {
                    $results[$commit['id']] = ['status' => 'failed', 'error' => $ex->getMessage()];
                }
            }

            return ['status' => $status, 'commits' => $results];
        }

        return ['status' => 'ignored', 'message' => 'Unusable payload.'];
    }

    /**
     * Wrapper for creating a new build.
     *
     * @param Project $project
     * @param string  $commitId
     * @param string  $branch
     * @param string  $tag
     * @param string  $committer
     * @param string  $commitMessage
     * @param array   $extra
     *
     * @return array
     *
     * @throws Exception
     */
    protected function createBuild(
        Project $project,
        $commitId,
        $branch,
        $tag,
        $committer,
        $commitMessage,
        array $extra = null
    ) {
        if ($project->getArchived()) {
            throw new NotFoundException(Lang::get('project_x_not_found', $project->getId()));
        }

        // Check if a build already exists for this commit ID:
        $builds = $this->buildStore->getByProjectAndCommit($project->getId(), $commitId);

        $ignore_environments = [];
        $ignore_tags         = [];
        if ($builds['count']) {
            foreach($builds['items'] as $build) {
                /** @var Build $build */
                $ignore_environments[$build->getId()] = $build->getEnvironment();
                $ignore_tags[$build->getId()]         = $build->getTag();
            }
        }

        // Check if this branch is to be built.
        if ($project->getDefaultBranchOnly() && ($branch !== $project->getBranch())) {
            return [
                'status'  => 'ignored',
                'message' => 'The branch is not a branch by default. Build is allowed only for the branch by default.'
            ];
        }

        $environments = $project->getEnvironmentsObjects();
        if ($environments['count']) {
            $created_builds    = [];
            $environment_names = $project->getEnvironmentsNamesByBranch($branch);
            // use base branch from project
            if (!empty($environment_names)) {
                $duplicates = [];
                foreach ($environment_names as $environment_name) {
                    if (
                        !in_array($environment_name, $ignore_environments) ||
                        ($tag && !in_array($tag, $ignore_tags, true))
                    ) {
                        // If not, create a new build job for it:
                        $build = $this->buildService->createBuild(
                            $project,
                            $environment_name,
                            $commitId,
                            $project->getBranch(),
                            $tag,
                            $committer,
                            $commitMessage,
                            Build::SOURCE_WEBHOOK,
                            0,
                            $extra
                        );

                        $created_builds[] = [
                            'id'          => $build->getID(),
                            'environment' => $environment_name,
                        ];
                    } else {
                        $duplicates[] = array_search($environment_name, $ignore_environments);
                    }
                }
                if (!empty($created_builds)) {
                    if (empty($duplicates)) {
                        return ['status' => 'ok', 'builds' => $created_builds];
                    } else {
                        return ['status' => 'ok', 'builds' => $created_builds, 'message' => sprintf('For this commit some builds already exists (%s)', implode(', ', $duplicates))];
                    }
                } else {
                    return ['status' => 'ignored', 'message' => sprintf('For this commit already created builds (%s)', implode(', ', $duplicates))];
                }
            } else {
                return ['status' => 'ignored', 'message' => 'Branch not assigned to any environment'];
            }
        } else {
            $environment_name = null;
            if (
                !in_array($environment_name, $ignore_environments, true) ||
                ($tag && !in_array($tag, $ignore_tags, true))
            ) {
                $build = $this->buildService->createBuild(
                    $project,
                    null,
                    $commitId,
                    $branch,
                    $tag,
                    $committer,
                    $commitMessage,
                    Build::SOURCE_WEBHOOK,
                    0,
                    $extra
                );

                return ['status' => 'ok', 'buildID' => $build->getID()];
            } else {
                return [
                    'status'  => 'ignored',
                    'message' => sprintf('Duplicate of build #%d', array_search($environment_name, $ignore_environments)),
                ];
            }
        }
    }

    /**
     * Fetch a project and check its type.
     *
     * @param int|string   $projectId    id or title of project
     * @param array|string $expectedType
     *
     * @return Project
     *
     * @throws Exception If the project does not exist or is not of the expected type.
     */
    protected function fetchProject($projectId, $expectedType)
    {
        if (empty($projectId)) {
            throw new Exception('Project does not exist: ' . $projectId);
        }

        if (is_numeric($projectId)) {
            $project = $this->projectStore->getById((integer)$projectId);
        } else {
            $projects = $this->projectStore->getByTitle($projectId, 2);
            if ($projects['count'] < 1) {
                throw new Exception('Project does not found: ' . $projectId);
            }
            if ($projects['count'] > 1) {
                throw new Exception('Project id is ambiguous: ' . $projectId);
            }
            $project = reset($projects['items']);
        }

        if (is_array($expectedType)
            ? !in_array($project->getType(), $expectedType)
            : $project->getType() !== $expectedType
        ) {
            throw new Exception('Wrong project type: ' . $project->getType());
        }

        return $project;
    }
}
