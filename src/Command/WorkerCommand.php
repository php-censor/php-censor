<?php

namespace PHPCensor\Command;

use PHPCensor\Config;
use Monolog\Logger;
use PHPCensor\Logging\OutputLogHandler;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Worker Command - Starts the BuildWorker, which pulls jobs from beanstalkd
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class WorkerCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Monolog\Logger $logger
     * @param string          $name
     */
    public function __construct(Logger $logger, $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:worker')

            ->addOption('debug', null, null, 'Run PHP Censor in Debug Mode')

            ->setDescription('Runs the PHP Censor build worker.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        // For verbose mode we want to output all informational and above
        // messages to the symphony output interface.
        if ($input->hasOption('verbose') && $input->getOption('verbose')) {
            $this->logger->pushHandler(
                new OutputLogHandler($this->output, Logger::INFO)
            );
        }

        // Allow to run in "debug mode"
        if ($input->hasOption('debug') && $input->getOption('debug')) {
            $output->writeln('<comment>Debug mode enabled.</comment>');
            define('DEBUG_MODE', true);
        }

        $config = Config::getInstance()->get('php-censor.queue', []);
        if (empty($config['host']) || empty($config['name'])) {
            throw new \RuntimeException(
                'The worker is not configured. You must set a host and queue in your config.yml file.'
            );
        }

        $worker = new BuildWorker($config['host'], $config['name']);

        $worker->setLogger($this->logger);
        $worker->startWorker();
    }
}
