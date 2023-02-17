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
use PHPCensor\StoreRegistry;
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

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        LoggerInterface $logger,
        BuildService $buildService,
        BuildFactory $buildFactory,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $storeRegistry, $logger, $name);

        $this->buildService = $buildService;
        $this->buildFactory = $buildFactory;
    }

    protected function configure(): void
    {
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
                "Gracefully stop one worker ('soon' = when next job done, 'done' = when current jobs done, 'idle' = when waiting for jobs)",
                ''
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

        $periodicalWork = (bool)$input->getOption('periodical-work');
        $worker = new BuildWorker(
            $this->configuration,
            $this->databaseManager,
            $this->storeRegistry,
            $this->logger,
            $this->buildService,
            $this->buildFactory,
            $config['host'],
            (int)$this->configuration->get('php-censor.queue.port', PheanstalkInterface::DEFAULT_PORT),
            $config['name'],
            $periodicalWork
        );

        $worker->startWorker();

        return 0;
    }
}
