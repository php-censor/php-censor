<?php

namespace PHPCensor\Command;

use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove old builds
 *
 * @author David Sloan <dave@d3r.com>
 */
class RemoveOldBuildsCommand extends Command
{
    /**
     * @var ProjectStore
     */
    protected $projectStore;

    /**
     * @var BuildService
     */
    protected $buildService;

    public function __construct(ProjectStore $projectStore, BuildService $buildService)
    {
        parent::__construct();

        /** @var ProjectStore $projectStore */
        $this->projectStore = $projectStore;

        /** @var BuildStore $buildStore */
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
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->projectStore->getAll();
        foreach ($projects['items'] as $project) {
            $this->buildService->deleteOldByProject($project->getId());
        }
    }
}
