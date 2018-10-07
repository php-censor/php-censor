<?php

namespace PHPCensor\Command;

use PHPCensor\Config;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Worker Command - Starts the BuildWorker, which pulls jobs from beanstalkd
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class WorkerCommand extends LoggingCommand
{
    protected function configure()
    {
        $this
            ->setName('php-censor:worker')
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

        (new BuildWorker($this->logger, $config['host'], $config['name']))
            ->startWorker();
    }
}
