<?php

declare(strict_types=1);

namespace PHPCensor\Worker;

use DateTime;
use Exception;
use Monolog\Level;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\Job;
use Pheanstalk\Values\TubeName;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use Psr\Log\LoggerInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildWorker
{
    final public const JOB_TYPE_BUILD     = 'php-censor.build';
    final public const JOB_TYPE_STOP_FLAG = 'php-censor.stop-flag';

    /**
     * Seconds to wait for a job when reserving it.
     */
    private const RESERVE_TIMEOUT = 5;

    /**
     * If this variable changes to false, the worker will stop after the current build.
     */
    private bool $canRun = true;

    private readonly Pheanstalk $pheanstalk;

    /**
     * Whether the current queue connection uses and watches the configured tube.
     */
    private bool $subscribed = false;

    private int $lastPeriodical = 0;

    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly DatabaseManager $databaseManager,
        private readonly StoreRegistry $storeRegistry,
        /**
         * The logger for builds to use.
         */
        private readonly LoggerInterface $logger,
        private readonly BuildService $buildService,
        private readonly BuildFactory $buildFactory,
        string $queueHost,
        int $queuePort,
        /**
         * beanstalkd queue to watch
         */
        private readonly string $queueTube,
        private readonly bool $canPeriodicalWork
    ) {
        $this->pheanstalk = Pheanstalk::create($queueHost, $queuePort);
    }

    public function stopWorker(): void
    {
        $this->canRun = false;
    }

    public function startWorker(): void
    {
        $this->canRun = true;

        $this->runWorker();
    }

    protected function runWorker(): void
    {
        $this->subscribe();

        while ($this->canRun) {
            if ($this->canPeriodicalWork &&
                $this->canRunPeriodicalWork()) {
                $this->buildService->createPeriodicalBuilds($this->logger);
            }

            if ($this->canForceRewindLoop()) {
                \sleep(1);

                continue;
            }

            $job = $this->reserveJob();
            if (null === $job) {
                continue;
            }

            $jobData = \json_decode($job->getData(), true);

            if (!$this->verifyJob($job)) {
                $this->deleteJob($job);

                continue;
            }

            $this->logger->notice(
                \sprintf(
                    'Received build #%s from the queue tube "%s".',
                    $jobData['build_id'],
                    $this->queueTube
                )
            );

            $build = $this->buildFactory->getBuildById((int)$jobData['build_id']);
            if (!$build) {
                $this->logger->warning(
                    \sprintf(
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
                    \sprintf(
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

            /** @var SecretStore $secretStore */
            $secretStore = $this->storeRegistry->get('Secret');

            // Logging relevant to this build should be stored against the build itself.
            $buildDbLog = new BuildDBLogHandler($secretStore, $buildStore, $build, Level::Debug);
            $this->logger->pushHandler($buildDbLog);

            $buildLogger = new BuildLogger($this->logger, $build);
            $builder = new Builder(
                $this->configuration,
                $this->databaseManager,
                $this->storeRegistry,
                $build,
                $buildLogger
            );

            try {
                $builder->execute();
            } catch (\Throwable $e) {
                $builder->getBuildLogger()->log('');
                $builder->getBuildLogger()->logFailure(
                    \sprintf(
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

    protected function deleteJob(Job $job): void
    {
        try {
            $this->pheanstalk->delete($job);
        } catch (\Throwable $e) {
            $this->logger->warning($e->getMessage());

            // The connection may have been lost: Pheanstalk would silently reconnect on the "default" tube.
            $this->subscribed = false;
        }
    }

    /**
     * Uses (for peeking) and watches (for reserving) only the configured queue tube.
     *
     * Must be called again after a lost connection: Pheanstalk reconnects transparently, but the new connection
     * is back on the "default" tube.
     */
    protected function subscribe(): void
    {
        $tube = new TubeName($this->queueTube);

        $this->pheanstalk->useTube($tube);
        $this->pheanstalk->watch($tube);
        if ((string)$tube !== (string)TubeName::default()) {
            $this->pheanstalk->ignore(TubeName::default());
        }

        $this->subscribed = true;
    }

    /**
     * Reserves the next job without blocking the loop (and the periodical work) forever.
     */
    protected function reserveJob(): ?Job
    {
        try {
            return $this->pheanstalk->reserveWithTimeout(self::RESERVE_TIMEOUT);
        } catch (\Throwable $e) {
            $this->logger->error(
                \sprintf('Unable to reserve a job from the queue tube "%s": %s', $this->queueTube, $e->getMessage())
            );

            $this->resubscribe();

            return null;
        }
    }

    protected function resubscribe(): void
    {
        $this->subscribed = false;

        try {
            $this->pheanstalk->disconnect();
            $this->subscribe();
        } catch (\Throwable $e) {
            $this->logger->error(\sprintf('Unable to reconnect to the queue: %s', $e->getMessage()));

            \sleep(5);
        }
    }

    /**
     * Rewinds the loop (to let the periodical work run) when no job is ready in the queue tube.
     */
    protected function canForceRewindLoop(): bool
    {
        if (!$this->subscribed) {
            $this->resubscribe();
            if (!$this->subscribed) {
                return true;
            }
        }

        try {
            return null === $this->pheanstalk->peekReady();
        } catch (\Throwable $e) {
            $this->logger->warning(\sprintf('Unable to peek the queue tube "%s": %s', $this->queueTube, $e->getMessage()));

            $this->resubscribe();

            return true;
        }
    }

    protected function canRunPeriodicalWork(): bool
    {
        $currentTime = \time();
        if (($this->lastPeriodical + 60) > $currentTime) {
            return false;
        }

        $this->lastPeriodical = $currentTime;

        return true;
    }

    protected function verifyJob(Job $job): bool
    {
        $jobData = \json_decode($job->getData(), true);

        if (empty($jobData) || !\is_array($jobData)) {
            $this->logger->warning(
                \sprintf('Empty job (#%s) from the queue tube "%s"!', $job->getId(), $this->queueTube)
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
                \sprintf(
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
