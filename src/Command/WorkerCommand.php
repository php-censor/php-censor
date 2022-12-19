<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use Exception;
use Pheanstalk\Contract\PheanstalkInterface;
use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildMetaStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\Worker\BuildWorker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Common\Exception\InvalidArgumentException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Dan Cryer <dan@block8.co.uk>
 */
class WorkerCommand extends Command
{
    private const MIN_QUEUE_PRIORITY = 24;
    private const MAX_QUEUE_PRIORITY = 2025;

    protected BuildService $buildService;

    protected BuildFactory $buildFactory;

    protected BuildStore $buildStore;

    protected EnvironmentStore $environmentStore;

    protected SecretStore $secretStore;

    protected BuildErrorStore $buildErrorStore;

    protected BuildMetaStore $buildMetaStore;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        BuildMetaStore $buildMetaStore,
        BuildErrorStore $buildErrorStore,
        BuildStore $buildStore,
        SecretStore $secretStore,
        EnvironmentStore $environmentStore,
        LoggerInterface $logger,
        BuildService $buildService,
        BuildFactory $buildFactory,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $logger, $name);

        $this->buildService     = $buildService;
        $this->buildFactory     = $buildFactory;
        $this->buildStore       = $buildStore;
        $this->secretStore      = $secretStore;
        $this->environmentStore = $environmentStore;
        $this->buildErrorStore  = $buildErrorStore;
        $this->buildMetaStore   = $buildMetaStore;
    }

    protected function configure(): void
    {
        $whenHints = 'soon=when next job done (default), done=when current jobs done, idle=when waiting for jobs';
        $this
            ->setName('php-censor:worker')
            ->addOption(
                'periodical-work',
                'p',
                InputOption::VALUE_NONE,
                'Allow worker run periodical work'
            )
            ->addOption(
                'stop-worker',
                's',
                InputOption::VALUE_OPTIONAL,
                "Gracefully stop one worker ($whenHints)"
            )
            ->setDescription('Runs the PHP Censor build worker.');
    }

    /**
     * @throws RuntimeException
     */
    private function checkQueueSettings(array $config): void
    {
        if (empty($config['host']) || empty($config['name'])) {
            throw new RuntimeException(
                'The worker is not configured. You must set a host and queue in your config.yml file.'
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function processWorkerStopFlag(InputInterface $input): bool
    {
        $value = $input->getOption('stop-worker');
        if (!empty($value)) {
            if ('soon' === $value) {
                $priority = self::MIN_QUEUE_PRIORITY; // high priority, stop soon
            } elseif ('done' === $value) {
                $priority = PheanstalkInterface::DEFAULT_PRIORITY;
            } elseif ('idle' === $value) {
                $priority = self::MAX_QUEUE_PRIORITY; // low priority, stop late
            } else {
                throw new InvalidArgumentException(
                    \sprintf('Invalid value "%s" for --stop-worker, valid are soon, done and idle;', $value)
                );
            }

            $jobData = [];
            $this->buildService->addJobToQueue(BuildWorker::JOB_TYPE_STOP_FLAG, $jobData, $priority);

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $config = $this->configuration->get('php-censor.queue', []);
        $this->checkQueueSettings($config);

        $needToStopWorker = $this->processWorkerStopFlag($input);
        if ($needToStopWorker) {
            return 0;
        }

        $canPeriodicalWork = $input->hasOption('periodical-work') && $input->getOption('periodical-work');
        $worker = new BuildWorker(
            $this->configuration,
            $this->databaseManager,
            $this->buildMetaStore,
            $this->buildErrorStore,
            $this->buildStore,
            $this->secretStore,
            $this->environmentStore,
            $this->logger,
            $this->buildService,
            $this->buildFactory,
            $config['host'],
            (int)$this->configuration->get('php-censor.queue.port', PheanstalkInterface::DEFAULT_PORT),
            $config['name'],
            $canPeriodicalWork
        );

        $worker->startWorker();

        return 0;
    }
}
