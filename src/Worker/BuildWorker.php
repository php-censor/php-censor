<?php

declare(strict_types = 1);

namespace PHPCensor\Worker;

use DateTime;
use Exception;
use Monolog\Logger;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\StoreRegistry;

class BuildWorker
{
    const JOB_TYPE_BUILD     = 'php-censor.build';
    const JOB_TYPE_STOP_FLAG = 'php-censor.stop-flag';

    /**
     * If this variable changes to false, the worker will stop after the current build.
     */
    private bool $canRun = true;

    private bool $canPeriodicalWork;

    /**
     * The logger for builds to use.
     */
    private Logger $logger;

    private BuildService $buildService;

    private ConfigurationInterface $configuration;

    private DatabaseManager $databaseManager;

    private StoreRegistry $storeRegistry;

    /**
     * beanstalkd queue to watch
     */
    private string $queueTube;

    private Pheanstalk $pheanstalk;

    private int $lastPeriodical;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        Logger $logger,
        BuildService $buildService,
        string $queueHost,
        int $queuePort,
        string $queueTube,
        bool $canPeriodicalWork
    ) {
        $this->logger          = $logger;
        $this->buildService    = $buildService;
        $this->configuration   = $configuration;
        $this->databaseManager = $databaseManager;
        $this->storeRegistry   = $storeRegistry;

        $this->queueTube  = $queueTube;
        $this->pheanstalk = Pheanstalk::create($queueHost, $queuePort);

        $this->lastPeriodical    = 0;
        $this->canPeriodicalWork = $canPeriodicalWork;
    }

    public function stopWorker()
    {
        $this->canRun = false;
    }

    public function startWorker()
    {
        $this->canRun = true;

        $this->runWorker();
    }

    protected function runWorker()
    {
        $this->pheanstalk->watchOnly($this->queueTube);

        $buildStore = $this->storeRegistry->get('Build');

        while ($this->canRun) {
            if ($this->canPeriodicalWork &&
                $this->canRunPeriodicalWork()) {
                $this->buildService->createPeriodicalBuilds($this->logger);
            }

            if ($this->canForceRewindLoop()) {
                sleep(1);

                continue;
            }

            $job     = $this->pheanstalk->reserve();
            $jobData = json_decode($job->getData(), true);

            if (!$this->verifyJob($job)) {
                $this->deleteJob($job);

                continue;
            }

            $this->logger->notice(
                sprintf(
                    'Received build #%s from the queue tube "%s".',
                    $jobData['build_id'],
                    $this->queueTube
                )
            );

            $build = BuildFactory::getBuildById(
                $this->configuration,
                $this->storeRegistry,
                (int)$jobData['build_id']
            );

            if (!$build) {
                $this->logger->warning(
                    sprintf(
                        'Build #%s from the queue tube "%s" does not exist in the database!',
                        $jobData['build_id'],
                        $this->queueTube
                    )
                );

                $this->deleteJob($job);

                continue;
            }

            if (Build::STATUS_PENDING !== $build->getStatus()) {
                $this->logger->warning(
                    sprintf(
                        'Invalid build #%s status "%s" from the queue tube "%s". ' .
                        'Build status should be "%s" (pending)!',
                        $build->getId(),
                        $build->getStatus(),
                        $this->queueTube,
                        Build::STATUS_PENDING
                    )
                );

                $this->deleteJob($job);

                continue;
            }

            /** @var BuildStore $buildStore */
            $buildStore = $this->storeRegistry->get('Build');

            // Logging relevant to this build should be stored against the build itself.
            $buildDbLog = new BuildDBLogHandler($buildStore, $build, Logger::DEBUG);
            $this->logger->pushHandler($buildDbLog);

            $builder = new Builder(
                $this->configuration,
                $this->databaseManager,
                $this->storeRegistry,
                $build,
                $this->logger
            );

            try {
                $builder->execute();
            } catch (Exception $e) {
                $builder->getBuildLogger()->log('');
                $builder->getBuildLogger()->logFailure(
                    sprintf(
                        'BUILD FAILED! Exception: %s',
                        $e->getMessage()
                    ),
                    $e
                );

                $build->setStatusFailed();
                $build->setFinishDate(new DateTime());

                $buildStore->save($build);

                $build->sendStatusPostback();
            }

            // After execution we no longer want to record the information back to this specific build so the handler
            // should be removed.
            $this->logger->popHandler();
            // destructor implicitly call flush
            unset($buildDbLog);

            $this->deleteJob($job);
        }
    }

    /**
     * @param Job $job
     */
    protected function deleteJob(Job $job)
    {
        try {
            $this->pheanstalk->delete($job);
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    protected function canForceRewindLoop()
    {
        try {
            $peekedJob = $this->pheanstalk->peekReady($this->queueTube);
        } catch (Exception $e) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function canRunPeriodicalWork()
    {
        $currentTime = time();
        if (($this->lastPeriodical + 60) > $currentTime) {
            return false;
        }

        $this->lastPeriodical = $currentTime;

        return true;
    }

    /**
     * @param Job $job
     *
     * @return bool
     */
    protected function verifyJob(Job $job)
    {
        $jobData = json_decode($job->getData(), true);

        if (empty($jobData) || !is_array($jobData)) {
            $this->logger->warning(
                sprintf('Empty job (#%s) from the queue tube "%s"!', $job->getId(), $this->queueTube)
            );

            return false;
        }

        $jobType = !empty($jobData['type'])
            ? $jobData['type']
            : '';

        if (self::JOB_TYPE_STOP_FLAG === $jobType) {
            $this->stopWorker(); // stop worker on next loop

            return false;
        } elseif (self::JOB_TYPE_BUILD !== $jobType) {
            $this->logger->warning(
                sprintf(
                    'Invalid job (#%s) type "%s" in the queue tube "%s"!',
                    $job->getId(),
                    $jobType,
                    $this->queueTube
                )
            );

            return false;
        }

        return true;
    }
}
