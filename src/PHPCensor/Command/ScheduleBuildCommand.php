<?php

namespace PHPCensor\Command;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Schedules build command - creates a build for a project if it hasn't run for a specified time
 *
 * @author Vincent Vermeulen <vincent@redant.nl>
 */
class ScheduleBuildCommand extends Command
{
    /**
     * @var ProjectStore
     */
    protected $projectStore;

    /**
     * @var BuildStore
     */
    protected $buildStore;

    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * @param ProjectStore $projectStore
     * @param BuildStore   $buildStore
     * @param BuildService $buildService
     */
    public function __construct(ProjectStore $projectStore, BuildStore $buildStore, BuildService $buildService)
    {
        parent::__construct();

        $this->projectStore = $projectStore;
        $this->buildService = $buildService;
        $this->buildStore   = $buildStore;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('php-censor:schedule-build')
            ->setDescription('Schedules a build for active projects which have not been ran by X days')
            ->addArgument('days', InputArgument::REQUIRED, 'Since specified days');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sinceDays  = $input->getArgument('days');
        $date       = new \DateTime('now');
        $difference = new \DateInterval("P{$sinceDays}D");
        $date->sub($difference);

        $projects = $this->projectStore->getAll();
        $projects = $projects['items'];
        /** @var Project $project */
        foreach ($projects as $project) {
            $latestBuild = $this->buildStore->getLatestBuilds($project->getId(), 1);

            if ($latestBuild) {
                /** @var Build $build */
                $build  = $latestBuild[0];
                $status = (integer)$build->getStatus();
                if ($status === Build::STATUS_RUNNING || $status === Build::STATUS_PENDING) {
                    // If it's running or just created, we don't want to reschedule already.
                    continue;
                }
                if ($date < $build->getFinishDate()) {
                    // If finished date is newer then the specified since days, we don't want to reschedule
                    continue;
                }
            }

            try {
                $this->buildService->createBuild($project, null, '', null, null, null, null, Build::SOURCE_PERIODICAL);
                $output->writeln("Build Created for {$project->getTitle()}");
            } catch (\Exception $e) {
                $output->writeln('<error>Failed</error>');
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }
    }
}
