<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use PHPCensor\Common\Application\ConfigurationInterface;
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
    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        LoggerInterface $logger,
        protected ProjectStore $projectStore,
        protected BuildService $buildService,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $storeRegistry, $logger, $name);
    }

    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this
            ->setName('php-censor:remove-old-builds')
            ->setDescription('Remove old builds.');
    }

    /**
     * Loops through projects.
     *
     * @throws HttpException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projects = $this->projectStore->getAll();
        foreach ($projects['items'] as $project) {
            $this->buildService->deleteOldByProject($project->getId());
        }

        return 0;
    }
}
