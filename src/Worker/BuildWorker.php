<?php

namespace PHPCensor\Worker;

use DateTime;
use Exception;
use Monolog\Logger;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\Factory;

class BuildWorker
{
    public const JOB_TYPE_BUILD     = 'php-censor.build';
    public const JOB_TYPE_STOP_FLAG = 'php-censor.stop-flag';

    /**
     * If this variable changes to false, the worker will stop after the current build.
     *
     * @var bool
     */
    protected $canRun = true;

    /**
     * @var bool
     */
    protected $canPeriodicalWork;

    /**
     * The logger for builds to use.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * beanstalkd queue to watch
     *
     * @var string
     */
    protected $queueTube;

    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var int
     */
    protected $lastPeriodical;

    /**
     * @param string $queueHost
     * @param int    $queuePort
     * @param string $queueTube
     * @param bool   $canPeriodicalWork
     */
    public function __construct(
        Logger $logger,
        BuildService $buildService,
        $queueHost,
        $queuePort,
        $queueTube,
        $canPeriodicalWork
    ) {
        $this->logger       = $logger;
        $this->buildService = $buildService;

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

        $buildStore = Factory::getStore('Build');

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

            $build = BuildFactory::getBuildById($jobData['build_id']);

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

            // Logging relevant to this build should be stored against the build itself.
            $buildDbLog = new BuildDBLogHandler($build, Logger::DEBUG);
            $this->logger->pushHandler($buildDbLog);

            $builder = new Builder($build, $this->logger);

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
