<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use PHPCensor\BuildFactory;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Dan Cryer <dan@block8.co.uk>
 */
class RebuildQueueCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php-censor:rebuild-queue')
            ->setDescription('Rebuilds the PHP Censor worker queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var BuildStore $buildStore */
        $buildStore = $this->storeRegistry->get('Build');

        /** @var ProjectStore $projectStore */
        $projectStore = $this->storeRegistry->get('Project');

        $result = $buildStore->getByStatus(0);

        $this->logger->info(\sprintf('Found %d builds', \count($result['items'])));

        $buildService = new BuildService(
            $this->configuration,
            $this->storeRegistry,
            $buildStore,
            $projectStore
        );

        while (\count($result['items'])) {
            $build   = \array_shift($result['items']);
            $build   = BuildFactory::getBuild($this->configuration, $this->storeRegistry, $build);
            $project = $build->getProject();

            $this->logger->info('Added build #' . $build->getId() . ' to queue.');
            $buildService->addBuildToQueue(
                $build,
                (null !== $project) ? $project->getBuildPriority() : Project::DEFAULT_BUILD_PRIORITY
            );
        }
    }
}
