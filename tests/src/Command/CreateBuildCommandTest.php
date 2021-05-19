<?php

namespace Tests\PHPCensor\Command;

use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\ConfigurationInterface;
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

    protected LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration  = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $this->logger         = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $projectMock          = $this->getMockBuilder('PHPCensor\\Model\\Project')->getMock();

        $projectStoreMock = $this->getMockBuilder('PHPCensor\\Store\\ProjectStore')
            ->getMock();
        $projectStoreMock->method('getById')
            ->will($this->returnValueMap([
                [1, 'read', $projectMock],
                [2, 'read', null],
            ]));

        $buildServiceMock = $this->getMockBuilder('PHPCensor\\Service\\BuildService')
            ->disableOriginalConstructor()
            ->getMock();

        $buildServiceMock->method('createBuild')
            ->withConsecutive(
                [$projectMock, null, null, null, null, null],
                [$projectMock, '92c8c6e', null, null, null, null],
                [$projectMock, null, 'master', null, null, null]
            );

        $this->command = new CreateBuildCommand($this->configuration, $this->logger, $projectStoreMock, $buildServiceMock);

        $this->application = new Application();
    }

    protected function getCommandTester()
    {
        $this->application->add($this->command);

        $command = $this->application->find('php-censor:create-build');
        $commandTester = new CommandTester($command);

        return $commandTester;
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
        self::expectException('\PHPCensor\Exception\InvalidArgumentException');

        $commandTester = $this->getCommandTester();
        $commandTester->execute(['projectId' => 2]);
    }
}
