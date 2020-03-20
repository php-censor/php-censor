<?php

namespace PHPCensor\Service;

use DateInterval;
use DateTime;
use Exception;
use Monolog\Logger;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use PHPCensor\BuildFactory;
use PHPCensor\Config;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * The build service handles the creation, duplication and deletion of builds.
 */
class BuildService
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
     * @var bool
     */
    public $queueError = false;

    /**
     * @param BuildStore   $buildStore
     * @param ProjectStore $projectStore
     */
    public function __construct(
        BuildStore $buildStore,
        ProjectStore $projectStore
    ) {
        $this->buildStore   = $buildStore;
        $this->projectStore = $projectStore;
    }

    /**
     * @param Project     $project
     * @param string      $environment
     * @param string      $commitId
     * @param string|null $branch
     * @param string|null $tag
     * @param string|null $committerEmail
     * @param string|null $commitMessage
     * @param int     $source
     * @param int     $userId
     * @param array|null  $extra
     *
     * @return Build
     */
    public function createBuild(
        Project $project,
        $environment,
        $commitId = '',
        $branch = null,
        $tag = null,
        $committerEmail = null,
        $commitMessage = null,
        $source = Build::SOURCE_UNKNOWN,
        $userId = 0,
        $extra = null
    ) {
        $build = new Build();
        $build->setCreateDate(new DateTime());
        $build->setProjectId($project->getId());
        $build->setStatusPending();
        $build->setEnvironment($environment);

        if (!is_null($extra)) {
            $build->setExtra($extra);
        }

        $branches = $project->getBranchesByEnvironment($environment);
        $build->addExtraValue('branches', $branches);

        $build->setSource($source);
        $build->setUserId($userId);
        $build->setCommitId((string)$commitId);

        if (!empty($branch)) {
            $build->setBranch($branch);
        } else {
            $build->setBranch($project->getDefaultBranch());
        }

        if (!empty($tag)) {
            $build->setTag($tag);
        }

        if (!empty($committerEmail)) {
            $build->setCommitterEmail($committerEmail);
        }

        if (!empty($commitMessage)) {
            $build->setCommitMessage($commitMessage);
        }

        /** @var Build $build */
        $build   = $this->buildStore->save($build);
        $buildId = $build->getId();

        if (!empty($buildId)) {
            $build = BuildFactory::getBuild($build);
            $build->sendStatusPostback();
            $this->addBuildToQueue($build, $project->getRelativeBuildPriority());
        }

        return $build;
    }

    /**
     * @param Logger $logger
     *
     * @throws HttpException
     */
    public function createPeriodicalBuilds(Logger $logger)
    {
        $periodicalConfig = null;
        if (file_exists(APP_DIR . 'periodical.yml')) {
            try {
                $periodicalConfig = (new Yaml())->parse(
                    file_get_contents(APP_DIR . 'periodical.yml')
                );
            } catch (ParseException $e) {
                $logger->error(
                    sprintf(
                        'Invalid periodical builds config ("app/periodical.yml")! Exception: %s',
                        $e->getMessage()
                    ),
                    $e
                );

                return;
            }
        }

        if (empty($periodicalConfig) ||
            empty($periodicalConfig['projects']) ||
            !is_array($periodicalConfig['projects'])) {
            $logger->warning('Empty periodical builds config ("app/periodical.yml")!');

            return;
        }

        $buildsCount = 0;
        foreach ($periodicalConfig['projects'] as $projectId => $projectConfig) {
            $project = $this->projectStore->getById((int)$projectId);

            if (!$project ||
                empty($projectConfig['interval']) ||
                empty($projectConfig['branches']) ||
                !is_array($projectConfig['branches'])) {
                $logger->warning(
                    sprintf(
                        'Invalid/empty section for project #%s ("app/periodical.yml")!',
                        $projectId
                    )
                );

                continue;
            }

            $date = new DateTime('now');

            try {
                $interval = new DateInterval($projectConfig['interval']);
            } catch (Exception $e) {
                $logger->error(
                    sprintf(
                        'Invalid datetime interval for project #%s! Exception: %s',
                        $projectId,
                        $e->getMessage()
                    ),
                    $e
                );

                return;
            }

            $date->sub($interval);

            foreach ($projectConfig['branches'] as $branch) {
                $latestBuild = $this->buildStore->getLatestBuildByProjectAndBranch($projectId, $branch);

                if ($latestBuild) {
                    $status = (int)$latestBuild->getStatus();
                    if ($status === Build::STATUS_RUNNING || $status === Build::STATUS_PENDING) {
                        continue;
                    }

                    if ($date <= $latestBuild->getFinishDate()) {
                        continue;
                    }
                }

                $buildsCount++;

                $this->createBuild(
                    $project,
                    null,
                    '',
                    $branch,
                    null,
                    null,
                    null,
                    Build::SOURCE_PERIODICAL
                );
            }
        }

        $logger->notice(
            sprintf(
                'Created %d periodical builds for %d projects.',
                $buildsCount,
                count($periodicalConfig['projects'])
            )
        );
    }

    /**
     * @param Build $originalBuild
     * @param int   $source
     *
     * @return Build
     *
     * @throws Exception
     */
    public function createDuplicateBuild(Build $originalBuild, $source)
    {
        $build = new Build();
        $build->setParentId($originalBuild->getId());
        $build->setProjectId($originalBuild->getProjectId());
        $build->setCommitId($originalBuild->getCommitId());
        $build->setBranch($originalBuild->getBranch());
        $build->setTag($originalBuild->getTag());
        $build->setCommitterEmail($originalBuild->getCommitterEmail());
        $build->setCommitMessage($originalBuild->getCommitMessage());
        $build->setExtra($originalBuild->getExtra());
        $build->setEnvironment($originalBuild->getEnvironment());
        $build->setSource($source);
        $build->setUserId($originalBuild->getUserId());
        $build->setCreateDate(new DateTime());
        $build->setStatusPending();

        /** @var Build $build */
        $build   = $this->buildStore->save($build);
        $buildId = $build->getId();

        if (!empty($buildId)) {
            $build = BuildFactory::getBuild($build);
            $build->sendStatusPostback();
            $this->addBuildToQueue($build);
        }

        return $build;
    }

    /**
     * @param int $projectId
     *
     * @throws HttpException
     */
    public function deleteOldByProject($projectId)
    {
        $keepBuilds = (int)Config::getInstance()->get('php-censor.build.keep_builds', 100);
        $builds     = $this->buildStore->getOldByProject((int)$projectId, $keepBuilds);

        /** @var Build $build */
        foreach ($builds['items'] as $build) {
            $build->removeBuildDirectory(true);
            $this->buildStore->delete($build);
        }
    }

    /**
     * @param int $projectId
     */
    public function deleteAllByProject($projectId)
    {
        $this->buildStore->deleteAllByProject((int)$projectId);

        try {
            $projectPaths = [
                RUNTIME_DIR . 'builds/' . $projectId . '/',
                PUBLIC_DIR . 'artifacts/pdepend/' . $projectId . '/',
                PUBLIC_DIR . 'artifacts/phpunit/' . $projectId . '/',
            ];

            $fileSystem = new Filesystem();

            foreach ($projectPaths as $projectPath) {
                if (is_link($projectPath)) {
                    // Remove the symlink without using recursive.
                    exec(sprintf('rm "%s"', $projectPath));
                } else {
                    $fileSystem->remove($projectPath);
                }
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Delete a given build.
     *
     * @param Build $build
     *
     * @return bool
     */
    public function deleteBuild(Build $build)
    {
        $build->removeBuildDirectory(true);

        return $this->buildStore->delete($build);
    }

    /**
     * Takes a build and puts it into the queue to be run (if using a queue)
     * @param Build $build
     * @param int   $relPriority priority in queue relative to default
     */
    public function addBuildToQueue(Build $build, $relPriority = 0)
    {
        $buildId = $build->getId();

        if (empty($buildId)) {
            return;
        }

        $jobData = [
            'build_id' => $buildId,
        ];

        $this->addJobToQueue(BuildWorker::JOB_TYPE_BUILD, $jobData);
    }

    /**
     * @param string $jobType
     * @param array  $jobData
     * @param int    $priority
     */
    public function addJobToQueue($jobType, array $jobData, $priority = PheanstalkInterface::DEFAULT_PRIORITY)
    {
        $config   = Config::getInstance();
        $settings = $config->get('php-censor.queue', []);

        if (!empty($settings['host']) && !empty($settings['name'])) {
            $jobData['type'] = $jobType;
            try {
                $pheanstalk = new Pheanstalk(
                    $settings['host'],
                    $config->get('php-censor.queue.port', Pheanstalk::DEFAULT_PORT)
                );

                $pheanstalk->useTube($settings['name']);
                $pheanstalk->put(
                    json_encode($jobData),
                    $priority,
                    PheanstalkInterface::DEFAULT_DELAY,
                    $config->get('php-censor.queue.lifetime', 600)
                );
            } catch (Exception $ex) {
                $this->queueError = true;
            }
        }
    }
}
