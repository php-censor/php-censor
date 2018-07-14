<?php

namespace PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Logging\LoggedBuildContextTidier;
use PHPCensor\Logging\OutputLogHandler;
use PHPCensor\Store\BuildStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Store\Factory;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\Model\Build;

/**
 * Run console command - Runs any pending builds.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class RunCommand extends Command
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
     * @var int
     */
    protected $maxBuilds = 10;

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
            ->setName('php-censor:run-builds')

            ->addOption('debug', null, null, 'Run PHP Censor in debug mode')

            ->setDescription('Run all pending PHP Censor builds');
    }

    /**
     * Pulls all pending builds from the database or queue and runs them.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \PHPCensor\Exception\HttpException
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

        // Allow PHP Censor to run in "debug mode"
        if ($input->hasOption('debug') && $input->getOption('debug')) {
            $output->writeln('<comment>Debug mode enabled.</comment>');
            define('DEBUG_MODE', true);
        }

        $this->logger->pushProcessor(new LoggedBuildContextTidier());
        $this->logger->addInfo('Finding builds to process');

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');
        $result     = $buildStore->getByStatus(Build::STATUS_PENDING, $this->maxBuilds);

        $this->logger->addInfo(sprintf('Found %d builds', count($result['items'])));

        $builds = 0;

        while (count($result['items'])) {
            $build = array_shift($result['items']);
            $build = BuildFactory::getBuild($build);

            // Skip build (for now) if there's already a build running in that project:
            if (!empty($running[$build->getProjectId()])) {
                $this->logger->addInfo(sprintf('Skipping Build %d - Project build already in progress.', $build->getId()));
                continue;
            }

            $builds++;

            // Logging relevant to this build should be stored
            // against the build itself.
            $buildDbLog = new BuildDBLogHandler($build, Logger::INFO);
            $this->logger->pushHandler($buildDbLog);

            try {
                $builder = new Builder($build, $this->logger);
                $builder->execute();
            } catch (\Exception $ex) {
                $this->logger->addError($ex->getMessage());

                $build->setStatus(Build::STATUS_FAILED);
                $build->setFinishDate(new \DateTime());
                $build->setLog($build->getLog() . PHP_EOL . PHP_EOL . $ex->getMessage());
                $buildStore->save($build);
                $build->sendStatusPostback();
            }

            // After execution we no longer want to record the information
            // back to this specific build so the handler should be removed.
            $this->logger->popHandler();
            // destructor implicitly call flush
            unset($buildDbLog);
        }

        $this->logger->addInfo('Finished processing builds.');

        return $builds;
    }

    /**
     * @param int $numBuilds
     */
    public function setMaxBuilds($numBuilds)
    {
        $this->maxBuilds = (int)$numBuilds;
    }
}
