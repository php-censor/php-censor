<?php

namespace PHPCensor\Plugin;

use GuzzleHttp\Client;
use PHPCensor\Builder;
use PHPCensor\Database;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

class Bitbucket extends Plugin
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $token;

    /** @var string */
    protected $projectKey;

    /** @var string */
    protected $repositorySlug;

    /** @var bool */
    protected $createTaskPerFail = true;

    /** @var bool */
    protected $createTaskIfFail = true;

    /** @var bool */
    protected $updateBuild = false;

    /** @var string */
    protected $message = '';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var Database
     */
    protected $pdo;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'bitbucket';
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->pdo = Database::getConnection('read');
        $this->httpClient = new Client();
        $this->url = trim($options['url']);
        $this->message = isset($options['message']) ? $options['message'] : '';
        $this->token = $options['token'];
        $this->projectKey = $options['project_key'];
        $this->repositorySlug = $options['repository_lug'];
        $this->createTaskPerFail = $options['create_task_per_fail'];
        $this->createTaskIfFail = $options['create_task_if_fail'];
        $this->updateBuild = $options['update_build'];

        if (empty($this->message)) {
            $this->message = '## PHP CENSOR Report' . PHP_EOL;
            $this->message .= '```' . PHP_EOL;
            $this->message .= '%STATS%' . PHP_EOL;
            $this->message .= '```' . PHP_EOL;
            $this->message .= '%BUILD_URI%?is_new=only_new#errors' . PHP_EOL . PHP_EOL;

            $buildSettings = $this->getBuilder()->getConfig('build_settings');
            if (isset($buildSettings['pdepend'])) {
                $config = $this->getBuilder()->getSystemConfig('php-censor');
                $censorUrl = $config['url'];

                $buildDirectory = $this->getBuild()->getBuildBranchDirectory();

                $summary = $censorUrl . '/artifacts/pdepend/' . $buildDirectory . '/summary.xml';
                $chart = $censorUrl . '/artifacts/pdepend/' . $buildDirectory . '/chart.svg';
                $pyramid = $censorUrl . '/artifacts/pdepend/' . $buildDirectory . '/pyramid.svg';

                $this->message .= sprintf('![Chart](%s "Pdepend Chart")', $chart);
                $this->message .= sprintf('![Pyramid](%s "Pdepend Pyramid")', $pyramid) . PHP_EOL;
                $this->message .= $summary . PHP_EOL;
            }
        }

        if (empty($this->url) || empty($this->message) || empty($this->token) || empty($this->projectKey) || empty($this->repositorySlug)) {
            throw new \Exception('Please define the url for bitbucket plugin!');
        }
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $plugins = $this->prepareResult();
        $message = $this->reportGenerator($this->buildResultComparator($plugins));
        if (!empty($message)) {
            $commentId = $this->createCommentInPullRequest($this->findPullRequestsByBranch(), $message);

            if ($this->createTaskIfFail) {
                $this->createTaskForCommentInPullRequest($commentId, 'pls fix php-censor report');
            }

            if ($this->createTaskPerFail) {
                foreach ($plugins as $plugin) {
                    if (!$plugin->isDegraded()) {
                        continue;
                    }

                    $this->createTaskForCommentInPullRequest($commentId, $plugin->generateTaskDescription());
                }
            }
        }

        if ($this->updateBuild) {
            $this->updateBuild();
        }

        return true;
    }

    /**
     * @return int|null
     */
    protected function findPullRequestsByBranch()
    {
        $endpoint = sprintf('/projects/%s/repos/%s/pull-requests', $this->projectKey, $this->repositorySlug);
        $response = $this->apiRequest($endpoint)->getBody();
        $response = json_decode($response, true);

        foreach ($response['values'] as $pullRequest) {
            if ($pullRequest['fromRef']['displayId'] === $this->getBuild()->getBranch()) {
                return (int)$pullRequest['id'];
            }
        }

        return null;
    }

    /**
     * @param int $pullRequestId
     * @param string $message
     * @return int
     */
    protected function createCommentInPullRequest($pullRequestId, $message)
    {
        $endpoint = sprintf(
            '/projects/%s/repos/%s/pull-requests/%s/comments',
            $this->projectKey,
            $this->repositorySlug,
            $pullRequestId
        );

        $response = $this->apiRequest($endpoint, 'post', ['text' => $message])->getBody();
        $response = json_decode($response, true);

        return (int)$response['id'];
    }

    /**
     * @param int $commentId
     * @param string $message
     */
    protected function createTaskForCommentInPullRequest($commentId, $message)
    {
        $this->apiRequest('/tasks', 'post', [
            'anchor' => [
                'id' => $commentId,
                'type' => 'COMMENT',
            ],
            'text' => $message,
        ]);
    }


    protected function updateBuild()
    {
        $endpoint = sprintf(
            '/commits/%s',
            $this->getBuild()->getCommitId()
        );

        switch ($this->getBuild()->getStatus()) {
            case Build::STATUS_SUCCESS:
                $state = 'SUCCESSFUL';
                break;
            case Build::STATUS_FAILED:
                $state = 'FAILED';
                break;
            default:
                $state = 'INPROGRESS';
        }

        $this->buildStatusRequest($endpoint, 'post', [
            'state' => $state,
            'key' => 'php-censor',
            'name' => 'PHP Censor',
            'url' => APP_URL . 'build/view/' . $this->getBuild()->getId(),
            'description' => '',
        ]);
    }

    /**
     * @return Util\Plugin[]
     */
    protected function prepareResult()
    {
        $masterBuildStats = $this->findBuildErrorStats($this->findLatestBuild('master'));
        $currentBuildStats = $this->findBuildErrorStats($this->findLatestBuild($this->build->getBranch()));

        if (empty($masterBuildStats) && empty($currentBuildStats)) {
            return [];
        }

        $plugins = array_unique(array_merge(array_keys($masterBuildStats), array_keys($currentBuildStats)));
        sort($plugins);

        $result = [];
        foreach ($plugins as $plugin) {
            $result[] = new Util\Plugin(
                $plugin,
                isset($masterBuildStats[$plugin]) ? $masterBuildStats[$plugin] : 0,
                isset($currentBuildStats[$plugin]) ? $currentBuildStats[$plugin] : 0
            );
        }

        return $result;
    }

    /**
     * @param Util\Plugin[] $plugins
     * @return array
     */
    protected function buildResultComparator(array $plugins)
    {
        $maxPluginNameLength = max(array_map('strlen', array_keys($plugins)));

        $lines = [];
        foreach ($plugins as $plugin) {
            $lines[] = $plugin->generateFormatedOutput($maxPluginNameLength);
        }

        return $lines;
    }

    protected function reportGenerator(array $stats)
    {
        $message = str_replace(['%STATS%'], [implode(PHP_EOL, $stats)], $this->message);

        return $this->builder->interpolate($message);
    }

    protected function findLatestBuild($branchName)
    {
        $query = 'SELECT max(id) FROM build WHERE project_id = :id AND branch = :branch_name';
        return $this->selectValue($query, [':id' => $this->getBuild()->getProjectId(), ':branch_name' => $branchName]);
    }

    protected function findBuildErrorStats($buildId)
    {
        $query = 'SELECT plugin, count(*) FROM build_error WHERE build_id = :id GROUP BY plugin';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $buildId]);
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    protected function selectValue($query, array $parameters)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchColumn();
    }

    protected function buildStatusRequest($endpoint, $method = 'get', array $jsonBody = null)
    {
        return $this->request($this->url . '/rest/build-status/1.0' . $endpoint, $method, $jsonBody);
    }

    protected function apiRequest($endpoint, $method = 'get', array $jsonBody = null)
    {
        return $this->request($this->url . '/rest/api/1.0' . $endpoint, $method, $jsonBody);
    }

    protected function request($endpoint, $method = 'get', array $jsonBody = null)
    {
        $options = ['headers' => ['Authorization' => 'Bearer ' . $this->token]];
        $jsonBody !== null && $options['json'] = $jsonBody;
        return $this->httpClient->request($method, $endpoint, $options);
    }
}
