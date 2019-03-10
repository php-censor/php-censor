<?php

namespace PHPCensor\Command;

use Monolog\Logger;
use Pheanstalk\Pheanstalk;
use PHPCensor\Config;
use PHPCensor\Service\BuildService;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Worker Command - Starts the BuildWorker, which pulls jobs from beanstalkd
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class WorkerCommand extends LoggingCommand
{
    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * @param Logger       $logger
     * @param BuildService $buildService
     * @param string       $name
     */
    public function __construct(
        Logger $logger,
        BuildService $buildService,
        $name = null
    )
    {
        parent::__construct($logger, $name);

        $this->buildService = $buildService;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:worker')
            ->addOption(
                'periodical-work',
                'p',
                InputOption::VALUE_NONE,
                'Allow worker run periodical work'
            )
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
        parent::execute($input, $output);

        $config = Config::getInstance()->get('php-censor.queue', []);
        if (empty($config['host']) || empty($config['name'])) {
            throw new \RuntimeException(
                'The worker is not configured. You must set a host and queue in your config.yml file.'
            );
        }

        (new BuildWorker(
            $this->logger,
            $this->buildService,
            $config['host'],
            Config::getInstance()->get('php-censor.queue.port', Pheanstalk::DEFAULT_PORT),
            $config['name'],
            ($input->hasOption('periodical-work') && $input->getOption('periodical-work'))
        ))
            ->startWorker();
    }
}
