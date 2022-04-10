<?php

declare(strict_types=1);

namespace PHPCensor\Service;

use DateInterval;
use DateTime;
use Exception;
use Monolog\Logger;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Contract\PheanstalkInterface;
use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * The build service handles the creation, duplication and deletion of builds.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildService
{
    private BuildStore $buildStore;

    private ProjectStore $projectStore;

    private ConfigurationInterface $configuration;

    private StoreRegistry $storeRegistry;

    private BuildFactory $buildFactory;

    public bool $queueError = false;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        BuildFactory $buildFactory,
        BuildStore $buildStore,
        ProjectStore $projectStore
    ) {
        $this->configuration = $configuration;
        $this->storeRegistry = $storeRegistry;
        $this->buildStore    = $buildStore;
        $this->projectStore  = $projectStore;
        $this->buildFactory  = $buildFactory;
    }

    public function createBuild(
        Project $project,
        ?int $environmentId = null,
        string $commitId = '',
        ?string $branch = null,
        ?string $tag = null,
        ?string $committerEmail = null,
        ?string $commitMessage = null,
        int $source = Build::SOURCE_UNKNOWN,
        ?int $userId = null,
        ?array $extra = null
    ): Build {
        $build = new Build($this->storeRegistry);
        $build->setCreateDate(new DateTime());
        $build->setProjectId($project->getId());
        $build->setStatusPending();
        $build->setEnvironmentid($environmentId);

        if (!\is_null($extra)) {
            $build->setExtra($extra);
        }

        $branches = $project->getBranchesByEnvironment($environmentId);
        $build->addExtraValue('branches', $branches);

        $build->setSource($source);

        if (!$userId) {
            $userId = null;
        }
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
            $project = $build->getProject();
            $build = $this->buildFactory->getBuild($build);
            $build->sendStatusPostback();
            $this->addBuildToQueue(
                $build,
                (null !== $project) ? $project->getBuildPriority() : Project::DEFAULT_BUILD_PRIORITY
            );
        }

        return $build;
    }

    /**
     * @throws HttpException
     */
    public function createPeriodicalBuilds(Logger $logger): void
    {
        $periodicalConfig = null;
        if (\file_exists(APP_DIR . 'periodical.yml')) {
            try {
                $periodicalConfig = (new Yaml())->parse(
                    \file_get_contents(APP_DIR . 'periodical.yml')
                );
            } catch (ParseException $e) {
                $logger->error(
                    \sprintf(
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
            !\is_array($periodicalConfig['projects'])) {
            $logger->warning('Empty periodical builds config ("app/periodical.yml")!');

            return;
        }

        $buildsCount = 0;
        foreach ($periodicalConfig['projects'] as $projectId => $projectConfig) {
            $project = $this->projectStore->getById((int)$projectId);

            if (!$project ||
                empty($projectConfig['interval']) ||
                empty($projectConfig['branches']) ||
                !\is_array($projectConfig['branches'])) {
                $logger->warning(
                    \sprintf(
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
                    \sprintf(
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
            \sprintf(
                'Created %d periodical builds for %d projects.',
                $buildsCount,
                \count($periodicalConfig['projects'])
            )
        );
    }

    /**
     * @throws Exception
     */
    public function createDuplicateBuild(Build $originalBuild, int $source): Build
    {
        $build = new Build($this->storeRegistry);
        $build->setParentId($originalBuild->getId());
        $build->setProjectId($originalBuild->getProjectId());
        $build->setCommitId($originalBuild->getCommitId());
        $build->setBranch($originalBuild->getBranch());
        $build->setTag($originalBuild->getTag());
        $build->setCommitterEmail($originalBuild->getCommitterEmail());
        $build->setCommitMessage($originalBuild->getCommitMessage());
        $build->setExtra($originalBuild->getExtra());
        $build->setEnvironmentId($originalBuild->getEnvironmentId());
        $build->setSource($source);
        $build->setUserId($originalBuild->getUserId());
        $build->setCreateDate(new DateTime());
        $build->setStatusPending();

        /** @var Build $build */
        $build   = $this->buildStore->save($build);
        $buildId = $build->getId();

        if (!empty($buildId)) {
            $build   = $this->buildFactory->getBuild($build);
            $project = $build->getProject();
            $build->sendStatusPostback();
            $this->addBuildToQueue(
                $build,
                (null !== $project) ? $project->getBuildPriority() : Project::DEFAULT_BUILD_PRIORITY
            );
        }

        return $build;
    }

    /**
     * @throws HttpException
     */
    public function deleteOldByProject(int $projectId): void
    {
        $keepBuilds = (int)$this->configuration->get('php-censor.build.keep_builds', 100);
        $builds     = $this->buildStore->getOldByProject((int)$projectId, $keepBuilds);

        /** @var Build $build */
        foreach ($builds['items'] as $build) {
            $build->removeBuildDirectory(true);
            $this->buildStore->delete($build);
        }
    }

    public function deleteAllByProject(int $projectId): void
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
                if (\is_link($projectPath)) {
                    // Remove the symlink without using recursive.
                    \exec(\sprintf('rm "%s"', $projectPath));
                } else {
                    $fileSystem->remove($projectPath);
                }
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Delete a given build.
     */
    public function deleteBuild(Build $build): bool
    {
        if (!$build->getId()) {
            return false;
        }

        $build->removeBuildDirectory(true);

        return $this->buildStore->delete($build);
    }

    /**
     * Takes a build and puts it into the queue to be run (if using a queue)
     *
     * @param int   $buildPriority priority in queue relative to default
     */
    public function addBuildToQueue(Build $build, int $buildPriority = Project::DEFAULT_BUILD_PRIORITY): void
    {
        $buildId = $build->getId();

        if (empty($buildId)) {
            return;
        }

        $jobData = [
            'build_id' => $buildId,
        ];

        $this->addJobToQueue(BuildWorker::JOB_TYPE_BUILD, $jobData, ($buildPriority + Project::OFFSET_BETWEEN_BUILD_AND_QUEUE));
    }

    public function addJobToQueue(string $jobType, array $jobData, int $queuePriority = PheanstalkInterface::DEFAULT_PRIORITY): void
    {
        $settings = $this->configuration->get('php-censor.queue', []);
        if (!empty($settings['host']) && !empty($settings['name'])) {
            $jobData['type'] = $jobType;
            try {
                $pheanstalk = Pheanstalk::create(
                    $settings['host'],
                    (int)$this->configuration->get('php-censor.queue.port', PheanstalkInterface::DEFAULT_PORT)
                );

                $pheanstalk->useTube($settings['name']);
                $pheanstalk->put(
                    \json_encode($jobData),
                    $queuePriority,
                    PheanstalkInterface::DEFAULT_DELAY,
                    $this->configuration->get('php-censor.queue.lifetime', 600)
                );
            } catch (Exception $ex) {
                $this->queueError = true;
            }
        }
    }
}
