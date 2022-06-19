<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
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
    private ConfigurationInterface $configuration;
    private DatabaseManager $databaseManager;
    private Logger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
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
        $projectMock  = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $projectStoreMock = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();
        $projectStoreMock->method('getById')
            ->will($this->returnValueMap([
                [1, 'read', $projectMock],
                [2, 'read', null],
            ]));

        $buildServiceMock = $this->getMockBuilder(BuildService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $buildServiceMock->method('createBuild')
            ->withConsecutive(
                [$projectMock, null, null, null, null, null],
                [$projectMock, '92c8c6e', null, null, null, null],
                [$projectMock, null, 'master', null, null, null]
            );

        $this->command = new CreateBuildCommand($this->configuration, $this->databaseManager, $storeRegistry, $this->logger, $projectStoreMock, $buildServiceMock);

        $this->application = new Application();
    }

    protected function getCommandTester(): CommandTester
    {
        $this->application->add($this->command);
        $command = $this->application->find('php-censor:create-build');

        return new CommandTester($command);
    }

    public function testExecute(): void
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute(['projectId' => 1]);
        $commandTester->execute(['projectId' => 1, '--commit' => '92c8c6e']);
        $commandTester->execute(['projectId' => 1, '--branch' => 'master']);

        self::assertTrue(true);
    }

    public function testExecuteWithUnknownProjectId(): void
    {
        self::expectException(InvalidArgumentException::class);

        $commandTester = $this->getCommandTester();
        $commandTester->execute(['projectId' => 2]);
    }
}
