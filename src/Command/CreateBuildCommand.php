<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Exception\HttpException;
use PHPCensor\Store\EnvironmentStore;

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

    protected function configure(): void
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

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws HttpException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectId   = (int)$input->getArgument('projectId');
        $commitId    = $input->getOption('commit');
        $branch      = $input->getOption('branch');
        $environment = $input->hasOption('environment') ? $input->getOption('environment') : null;
        $ciEmail     = $input->getOption('email');
        $ciMessage   = $input->getOption('message');

        /** @var Project $project */
        $project = $this->projectStore->getById($projectId);
        if (empty($project) || $project->getArchived()) {
            throw new InvalidArgumentException('Project does not exist: ' . $projectId);
        }

        $environmentId = null;
        if ($environment) {
            /** @var EnvironmentStore $environmentStore */
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
                BuildInterface::SOURCE_MANUAL_CONSOLE
            );

            $output->writeln('Build Created');

            return 0;
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed</error>');
            $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
        }

        return 1;
    }
}
