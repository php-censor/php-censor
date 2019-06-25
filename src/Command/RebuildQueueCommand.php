<?php

namespace PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\BuildFactory;
use PHPCensor\Logging\OutputLogHandler;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\Factory;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class RebuildQueueCommand extends Command
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
     * @param Logger $logger
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
            ->setName('php-censor:rebuild-queue')
            ->setDescription('Rebuilds the PHP Censor worker queue.');
    }

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

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');
        
        /** @var ProjectStore $projectStore */
        $projectStore = Factory::getStore('Project');

        $result = $buildStore->getByStatus(0);

        $this->logger->addInfo(sprintf('Found %d builds', count($result['items'])));

        $buildService = new BuildService($buildStore, $projectStore);

        while (count($result['items'])) {
            $build = array_shift($result['items']);
            $build = BuildFactory::getBuild($build);

            $this->logger->addInfo('Added build #' . $build->getId() . ' to queue.');
            $buildService->addBuildToQueue($build);
        }
    }
}
