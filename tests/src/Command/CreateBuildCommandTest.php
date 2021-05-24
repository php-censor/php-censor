<?php

namespace Tests\PHPCensor\Command;

use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBuildCommandTest extends TestCase
{
    /**
     * @var CreateAdminCommand|MockObject
     */
    protected $command;

    /**
     * @var Application|MockObject
     */
    protected $application;

    protected ConfigurationInterface $configuration;

    protected DatabaseManager $databaseManager;

    protected LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration   = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $this->databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $this->logger          = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $projectMock           = $this
            ->getMockBuilder('PHPCensor\Model\Project')
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $projectStoreMock = $this
            ->getMockBuilder('PHPCensor\Store\ProjectStore')
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();
        $projectStoreMock->method('getById')
            ->will($this->returnValueMap([
                [1, 'read', $projectMock],
                [2, 'read', null],
            ]));

        $buildServiceMock = $this->getMockBuilder('PHPCensor\Service\BuildService')
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

    protected function getCommandTester()
    {
        $this->application->add($this->command);
        $command = $this->application->find('php-censor:create-build');

        return new CommandTester($command);
    }

    public function testExecute()
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute(['projectId' => 1]);
        $commandTester->execute(['projectId' => 1, '--commit' => '92c8c6e']);
        $commandTester->execute(['projectId' => 1, '--branch' => 'master']);

        self::assertTrue(true);
    }

    public function testExecuteWithUnknownProjectId()
    {
        self::expectException('\PHPCensor\Common\Exception\InvalidArgumentException');

        $commandTester = $this->getCommandTester();
        $commandTester->execute(['projectId' => 2]);
    }
}
