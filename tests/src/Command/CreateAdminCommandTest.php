<?php

namespace Tests\PHPCensor\Command;

use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends TestCase
{
    /**
     * @var CreateAdminCommand|MockObject
     */
    protected $command;

    /**
     * @var Application|MockObject
     */
    protected $application;

    /**
     * @var MockObject|QuestionHelper
     */
    protected $helper;

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
        $this->logger  = $this->getMockBuilder('Monolog\Logger')->getMock();
        $userStoreMock = $this
            ->getMockBuilder('PHPCensor\Store\UserStore')
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();

        $this->command = new CreateAdminCommand($this->configuration, $this->databaseManager, $storeRegistry, $this->logger, $userStoreMock);

        $this->helper = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $this->application = new Application();
    }

    /**
     * @return CommandTester
     */
    protected function getCommandTester()
    {
        $this->application->getHelperSet()->set($this->helper, 'question');
        $this->application->add($this->command);

        return new CommandTester($this->command);
    }

    public function testExecute()
    {
        $this->helper->method('ask')->will($this->returnValue('test@example.com'));
        $this->helper->method('ask')->will($this->returnValue('A name'));
        $this->helper->method('ask')->will($this->returnValue('foobar123'));

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        self::assertEquals('User account created!' . PHP_EOL, $commandTester->getDisplay());
    }
}
