<?php

namespace PHPCensor\Command;

use b8\Store\Factory;
use Monolog\Logger;
use PHPCensor\Service\BuildService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Re-runs the last run build.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class RebuildCommand extends Command
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var boolean
     */
    protected $run;

    /**
     * @var int
     */
    protected $sleep;

    /**
     * @param \Monolog\Logger $logger
     * @param string $name
     */
    public function __construct(Logger $logger, $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:rebuild')
            ->setDescription('Re-runs the last run build.');
    }

    /**
    * Loops through running.
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new RunCommand($this->logger);
        $runner->setMaxBuilds(1);

        /** @var \PHPCensor\Store\BuildStore $store */
        $store = Factory::getStore('Build');
        $service = new BuildService($store);

        $builds = $store->getLatestBuilds(null, 1);
        $lastBuild = array_shift($builds);
        $service->createDuplicateBuild($lastBuild);

        $runner->run(new ArgvInput([]), $output);
    }

    /**
     * Called when log entries are made in Builder / the plugins.
     *
     * @see \PHPCensor\Builder::log()
     */
    public function logCallback($log)
    {
        $this->output->writeln($log);
    }
}
