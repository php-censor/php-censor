<?php

declare(strict_types = 1);

namespace PHPCensor\Command;

use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Exception\HttpException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author David Sloan <dave@d3r.com>
 */
class RemoveOldBuildsCommand extends Command
{
    protected ProjectStore $projectStore;

    protected BuildService $buildService;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        LoggerInterface $logger,
        ProjectStore $projectStore,
        BuildService $buildService,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $storeRegistry, $logger, $name);

        $this->projectStore = $projectStore;
        $this->buildService = $buildService;
    }

    /**
     * Configure.
     */
    protected function configure()
    {
        $this
            ->setName('php-censor:remove-old-builds')
            ->setDescription('Remove old builds.');
    }

    /**
     * Loops through projects.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws HttpException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->projectStore->getAll();
        foreach ($projects['items'] as $project) {
            $this->buildService->deleteOldByProject($project->getId());
        }
    }
}
