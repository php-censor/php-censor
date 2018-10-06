<?php

namespace PHPCensor\Worker;

use Pheanstalk\Exception\ServerException;
use PHPCensor\Store\Factory;
use Monolog\Logger;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Model\Build;

class BuildWorker
{
    const JOB_TYPE = 'php-censor.build';

    /**
     * If this variable changes to false, the worker will stop after the current build.
     *
     * @var bool
     */
    protected $canRun = true;

    /**
     * The logger for builds to use.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * beanstalkd queue to watch
     *
     * @var string
     */
    protected $queueTube;

    /**
     * @var \Pheanstalk\Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @param Logger $logger
     * @param string $queueHost
     * @param string $queueTube
     */
    public function __construct(
        Logger $logger,
        $queueHost,
        $queueTube
    )
    {
        $this->logger = $logger;

        $this->queueTube  = $queueTube;
        $this->pheanstalk = new Pheanstalk($queueHost);
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
            $job     = $this->pheanstalk->reserve();
            $jobData = json_decode($job->getData(), true);

            if (!$this->verifyJob($job)) {
                $this->deleteJob($job);

                continue;
            }

            $this->logger->addNotice(
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
                        'Invalid build #%s status "%s" from the queue tube "%s". Build status should be "%s" (pending)!',
                        $build->getId(),
                        $build->getStatus(),
                        $this->queueTube,
                        Build::STATUS_PENDING
                    )
                );

                $this->deleteJob($job);

                continue;
            }

            // Logging relevant to this build should be stored
            // against the build itself.
            $buildDbLog = new BuildDBLogHandler($build, Logger::INFO);
            $this->logger->pushHandler($buildDbLog);

            $builder = new Builder($build, $this->logger);

            try {
                $builder->execute();
            } catch (\Exception $ex) {
                $builder->getBuildLogger()->log('');
                $builder->getBuildLogger()->logFailure(
                    sprintf(
                        'BUILD FAILED! Exception: %s',
                        $build->getId(),
                        $ex->getMessage()
                    ),
                    $ex
                );

                $build->setStatusFailed();
                $build->setFinishDate(new \DateTime());

                $buildStore->save($build);

                $build->sendStatusPostback();
            }

            // After execution we no longer want to record the information
            // back to this specific build so the handler should be removed.
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
        } catch (ServerException $e) {
            $this->logger->warning($e->getMessage());
        }
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

        if (self::JOB_TYPE !== $jobType) {
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
