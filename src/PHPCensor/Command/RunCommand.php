<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Command;

use b8\Config;
use Monolog\Logger;
use PHPCensor\Helper\Lang;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Logging\LoggedBuildContextTidier;
use PHPCensor\Logging\OutputLogHandler;
use PHPCensor\Store\BuildStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use b8\Store\Factory;
use PHPCensor\Builder;
use PHPCensor\BuildFactory;
use PHPCensor\Model\Build;

/**
 * Run console command - Runs any pending builds.
 * 
 * @author     Dan Cryer <dan@block8.co.uk>
 * @package    PHPCI
 * @subpackage Console
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
    protected $maxBuilds = 100;

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
            ->setName('php-censor:run-builds')
            ->setDescription(Lang::get('run_all_pending'))
            ->addOption('debug', null, null, 'Run PHP Censor in Debug Mode');
    }

    /**
     * Pulls all pending builds from the database or queue and runs them.
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

        $running = $this->validateRunningBuilds();

        $this->logger->pushProcessor(new LoggedBuildContextTidier());
        $this->logger->addInfo(Lang::get('finding_builds'));
        
        /** @var BuildStore $store */
        $store  = Factory::getStore('Build');
        $result = $store->getByStatus(Build::STATUS_PENDING, $this->maxBuilds);

        $this->logger->addInfo(Lang::get('found_n_builds', count($result['items'])));

        $builds = 0;

        while (count($result['items'])) {
            $build = array_shift($result['items']);
            $build = BuildFactory::getBuild($build);

            // Skip build (for now) if there's already a build running in that project:
            if (in_array($build->getProjectId(), $running)) {
                $this->logger->addInfo(Lang::get('skipping_build', $build->getId()));
                $result['items'][] = $build;

                // Re-run build validator:
                $running = $this->validateRunningBuilds();
                continue;
            }

            $builds++;

            try {
                // Logging relevant to this build should be stored
                // against the build itself.
                $buildDbLog = new BuildDBLogHandler($build, Logger::INFO);
                $this->logger->pushHandler($buildDbLog);

                $builder = new Builder($build, $this->logger);
                $builder->execute();

                // After execution we no longer want to record the information
                // back to this specific build so the handler should be removed.
                $this->logger->popHandler();
            } catch (\Exception $ex) {
                $build->setStatus(Build::STATUS_FAILED);
                $build->setFinished(new \DateTime());
                $build->setLog($build->getLog() . PHP_EOL . PHP_EOL . $ex->getMessage());
                $store->save($build);
            }

        }

        $this->logger->addInfo(Lang::get('finished_processing_builds'));

        return $builds;
    }

    public function setMaxBuilds($numBuilds)
    {
        $this->maxBuilds = (int)$numBuilds;
    }

    protected function validateRunningBuilds()
    {
        /** @var \PHPCensor\Store\BuildStore $store */
        $store   = Factory::getStore('Build');
        $running = $store->getByStatus(Build::STATUS_RUNNING);
        $rtn     = [];

        $timeout = Config::getInstance()->get('php-censor.build.failed_after', 1800);

        foreach ($running['items'] as $build) {
            /** @var \PHPCensor\Model\Build $build */
            $build = BuildFactory::getBuild($build);

            $now = time();
            $start = $build->getStarted()->getTimestamp();

            if (($now - $start) > $timeout) {
                $this->logger->addInfo(Lang::get('marked_as_failed', $build->getId()));
                $build->setStatus(Build::STATUS_FAILED);
                $build->setFinished(new \DateTime());
                $store->save($build);
                $build->removeBuildDirectory();
                continue;
            }

            $rtn[$build->getProjectId()] = true;
        }

        return $rtn;
    }
}
