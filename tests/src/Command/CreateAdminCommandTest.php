<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends TestCase
{
    private CreateAdminCommand $command;
    private Application $application;
    private QuestionHelper $helper;
    private ConfigurationInterface $configuration;
    private DatabaseManager $databaseManager;
    private Logger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $this->databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();
        $this->logger = $this->getMockBuilder('Monolog\Logger')
            ->setConstructorArgs(['logger'])
            ->getMock();
        $userStoreMock = $this
            ->getMockBuilder('PHPCensor\Store\UserStore')
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();

        $this->command = new CreateAdminCommand($this->configuration, $this->databaseManager, $storeRegistry, $this->logger, $userStoreMock);

        $this->helper = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->onlyMethods(['ask'])
            ->getMock();

        $this->application = new Application();
    }

    protected function getCommandTester(): CommandTester
    {
        $this->application->getHelperSet()->set($this->helper, 'question');
        $this->application->add($this->command);

        return new CommandTester($this->command);
    }

    public function testExecute(): void
    {
        $this->helper->method('ask')->will($this->returnValue('test@example.com'));
        $this->helper->method('ask')->will($this->returnValue('A name'));
        $this->helper->method('ask')->will($this->returnValue('foobar123'));

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        self::assertEquals('User account created!' . PHP_EOL, $commandTester->getDisplay());
    }
}
