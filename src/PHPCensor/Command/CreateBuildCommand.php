<?php

namespace PHPCensor\Command;

use PHPCensor\Model\Build;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create build command - creates a build for a project
 *
 * @author Jérémy DECOOL (@jdecool)
 */
class CreateBuildCommand extends Command
{
    /**
     * @var ProjectStore
     */
    protected $projectStore;

    /**
     * @var BuildService
     */
    protected $buildService;

    /**
     * @param ProjectStore $projectStore
     * @param BuildService $buildService
     */
    public function __construct(ProjectStore $projectStore, BuildService $buildService)
    {
        parent::__construct();

        $this->projectStore = $projectStore;
        $this->buildService = $buildService;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('php-censor:create-build')
            ->setDescription('Create a build for a project')
            ->addArgument('projectId', InputArgument::REQUIRED, 'A project ID')
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'Commit ID to build')
            ->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Branch to build')
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Committer email')
            ->addOption('message', null, InputOption::VALUE_OPTIONAL, 'Commit message');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId   = $input->getArgument('projectId');
        $commitId    = $input->getOption('commit');
        $branch      = $input->getOption('branch');
        $environment = $input->hasOption('environment') ? $input->getOption('environment') : null;
        $ciEmail     = $input->getOption('email');
        $ciMessage   = $input->getOption('message');

        $project = $this->projectStore->getById($projectId);
        if (empty($project) || $project->getArchived()) {
            throw new \InvalidArgumentException('Project does not exist: ' . $projectId);
        }

        try {
            $this->buildService->createBuild($project, $environment, $commitId, $branch, null, $ciEmail, $ciMessage, Build::SOURCE_MANUAL_CONSOLE);
            $output->writeln('Build Created');
        } catch (\Exception $e) {
            $output->writeln('<error>Failed</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
