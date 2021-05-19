<?php

declare(strict_types = 1);

namespace PHPCensor\Command;

use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildService;
use PHPCensor\Store;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Jérémy DECOOL (@jdecool)
 */
class CreateBuildCommand extends Command
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

    protected function configure()
    {
        $this
            ->setName('php-censor:create-build')

            ->addArgument('projectId', InputArgument::REQUIRED, 'A project ID')
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'Commit ID to build')
            ->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Branch to build')
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Committer email')
            ->addOption('message', null, InputOption::VALUE_OPTIONAL, 'Commit message')

            ->setDescription('Create a build for a project');
    }

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
            throw new InvalidArgumentException('Project does not exist: ' . $projectId);
        }

        $environmentId = null;
        if ($environment) {
            /** @var Store\EnvironmentStore $environmentStore */
            $environmentStore  = $this->storeRegistry->get('Environment');
            $environmentObject = $environmentStore->getByNameAndProjectId($environment, $project->getId());
            if ($environmentObject) {
                $environmentId = $environmentObject->getId();
            }
        }

        try {
            $this->buildService->createBuild(
                $project,
                $environmentId,
                $commitId,
                $branch,
                null,
                $ciEmail,
                $ciMessage,
                Build::SOURCE_MANUAL_CONSOLE
            );

            $output->writeln('Build Created');
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
