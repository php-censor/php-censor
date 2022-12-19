<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Exception\HttpException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Dan Cryer <dan@block8.co.uk>
 */
class RebuildQueueCommand extends Command
{
    protected BuildStore $buildStore;
    protected BuildErrorStore $buildErrorStore;
    protected ProjectStore $projectStore;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        LoggerInterface $logger,
        BuildStore $buildStore,
        BuildErrorStore $buildErrorStore,
        ProjectStore $projectStore,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $logger, $name);

        $this->buildStore      = $buildStore;
        $this->buildErrorStore = $buildErrorStore;
        $this->projectStore    = $projectStore;
    }

    protected function configure(): void
    {
        $this
            ->setName('php-censor:rebuild-queue')
            ->setDescription('Rebuilds the PHP Censor worker queue.');
    }

    /**
     * @throws RuntimeException
     * @throws HttpException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->buildStore->getByStatus(0);

        $this->logger->info(\sprintf('Found %d builds', \count($result['items'])));

        $buildFactory = new BuildFactory(
            $this->configuration,
            $this->buildStore
        );

        $buildService = new BuildService(
            $this->configuration,
            $buildFactory,
            $this->buildStore,
            $this->buildErrorStore,
            $this->projectStore
        );

        while (\count($result['items'])) {
            $build   = \array_shift($result['items']);
            $build   = $buildFactory->getBuild($build);
            $project = $build->getProject();

            $this->logger->info('Added build #' . $build->getId() . ' to queue.');
            $buildService->addBuildToQueue(
                $build,
                (null !== $project) ? $project->getBuildPriority() : Project::DEFAULT_BUILD_PRIORITY
            );
        }

        return 0;
    }
}
