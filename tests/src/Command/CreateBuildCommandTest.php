<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBuildCommandTest extends TestCase
{
    private CreateBuildCommand $command;
    private Application $application;
    /**
     * @var ConfigurationInterface&\PHPUnit\Framework\MockObject\Stub
     */
    private \PHPUnit\Framework\MockObject\Stub $configuration;
    private DatabaseManager $databaseManager;
    private Logger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration   = $this->createStub(ConfigurationInterface::class);
        $this->databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $this->logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['logger'])
            ->getMock();
        $project  = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();
        $projectStore->method('getById')
            ->willReturnMap([
                [1, 'read', $project],
                [2, 'read', null],
            ]);

        $buildService = $this->createMock(BuildService::class);

        $expectedCalls = [
            [$project, null, null, null, null, null],
            [$project, '92c8c6e', null, null, null, null],
            [$project, null, 'master', null, null, null],
        ];
        $buildService->method('createBuild')
            ->willReturnCallback(function (...$arguments) use (&$expectedCalls): Build {
                self::assertEquals(\array_shift($expectedCalls), \array_slice($arguments, 0, 6));

                return $this->createStub(Build::class);
            });

        $this->command = new CreateBuildCommand($this->configuration, $this->databaseManager, $storeRegistry, $this->logger, $projectStore, $buildService);

        $this->application = new Application();
    }

    protected function getCommandTester(): CommandTester
    {
        $this->application->addCommand($this->command);
        $command = $this->application->find('php-censor:create-build');

        return new CommandTester($command);
    }

    public function testExecute(): void
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute(['project-id' => 1]);
        $commandTester->execute(['project-id' => 1, '--commit' => '92c8c6e']);
        $commandTester->execute(['project-id' => 1, '--branch' => 'master']);

        self::assertTrue(true);
    }

    public function testExecuteWithUnknownProjectId(): void
    {
        self::expectException(InvalidArgumentException::class);

        $commandTester = $this->getCommandTester();
        $commandTester->execute(['project-id' => 2]);
    }
}
