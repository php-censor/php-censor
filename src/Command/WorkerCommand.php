<?php

declare(strict_types = 1);

namespace PHPCensor\Command;

use Exception;
use Monolog\Logger;
use Pheanstalk\Pheanstalk;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Service\BuildService;
use PHPCensor\StoreRegistry;
use PHPCensor\Worker\BuildWorker;
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
    const MIN_QUEUE_PRIORITY = 24;
    const MAX_QUEUE_PRIORITY = 2025;

    protected BuildService $buildService;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        Logger $logger,
        BuildService $buildService,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $storeRegistry, $logger, $name);

        $this->buildService = $buildService;
    }

    protected function configure()
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
                "Gracefully stop one worker ($whenHints)",
                false // default value is used when option not given
            )
            ->setDescription('Runs the PHP Censor build worker.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $config = $this->configuration->get('php-censor.queue', []);
        if (empty($config['host']) || empty($config['name'])) {
            throw new RuntimeException(
                'The worker is not configured. You must set a host and queue in your config.yml file.'
            );
        }
        $value = $input->getOption('stop-worker');
        if (false !== $value) {
            if ('soon' === $value || null === $value) {
                $priority = self::MIN_QUEUE_PRIORITY; // high priority, stop soon
            } elseif ('done' === $value) {
                $priority = Pheanstalk::DEFAULT_PRIORITY;
            } elseif ('idle' === $value) {
                $priority = self::MAX_QUEUE_PRIORITY; // low priority, stop late
            } else {
                $msg = \sprintf('Invalid value "%s" for --stop-worker, valid are soon, done and idle;', $value);
                throw new InvalidArgumentException($msg);
            }
            $jobData = [];
            $this->buildService->addJobToQueue(BuildWorker::JOB_TYPE_STOP_FLAG, $jobData, $priority);

            return;
        }

        (new BuildWorker(
            $this->configuration,
            $this->databaseManager,
            $this->storeRegistry,
            $this->logger,
            $this->buildService,
            $config['host'],
            $this->configuration->get('php-censor.queue.port', Pheanstalk::DEFAULT_PORT),
            $config['name'],
            ($input->hasOption('periodical-work') && $input->getOption('periodical-work'))
        ))
            ->startWorker();
    }
}
