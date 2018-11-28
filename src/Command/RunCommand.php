<?php

namespace PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Logging\BuildDBLogHandler;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
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
class RunCommand extends LoggingCommand
{
    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * @var int
     */
    protected $maxBuilds = 10;

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
            ->setName('php-censor:run-builds')
            ->setDescription('Run all pending PHP Censor builds');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        $this->buildService->createPeriodicalBuilds($this->logger);

        $result = $buildStore->getByStatus(Build::STATUS_PENDING, $this->maxBuilds);

        $this->logger->notice(
            sprintf('Found %d pending builds', count($result['items']))
        );

        $builds = 0;

        while (count($result['items'])) {
            $builds++;

            $build = array_shift($result['items']);
            $build = BuildFactory::getBuild($build);

            // Logging relevant to this build should be stored
            // against the build itself.
            $buildDbLog = new BuildDBLogHandler($build, Logger::DEBUG);
            $this->logger->pushHandler($buildDbLog);

            $builder = new Builder($build, $this->logger);

            try {
                $builder->execute();
            } catch (\Exception $ex) {
                $builder->getBuildLogger()->log('');
                $builder->getBuildLogger()->logFailure(
                    sprintf(
                        'BUILD FAILED! Exception: %s',
                        $build->getId(),
                        $ex->getMessage()
                    ),
                    $ex
                );

                $build->setStatusFailed();
                $build->setFinishDate(new \DateTime());

                $buildStore->save($build);

                $build->sendStatusPostback();
            }

            // After execution we no longer want to record the information
            // back to this specific build so the handler should be removed.
            $this->logger->popHandler();
            // destructor implicitly call flush
            unset($buildDbLog);
        }

        $this->logger->notice('Finished processing builds.');

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
