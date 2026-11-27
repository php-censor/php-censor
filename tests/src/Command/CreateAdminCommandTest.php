<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Command;

use Monolog\Logger;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends TestCase
{
    private CreateAdminCommand $command;
    private Application $application;
    private QuestionHelper $helper;
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
        $userStore = $this
            ->getMockBuilder(UserStore::class)
            ->setConstructorArgs([$this->databaseManager, $storeRegistry])
            ->getMock();

        $this->command = new CreateAdminCommand($this->configuration, $this->databaseManager, $storeRegistry, $this->logger, $userStore);

        $this->helper = $this
            ->getMockBuilder(QuestionHelper::class)
            ->onlyMethods(['ask'])
            ->getMock();

        $this->application = new Application();
    }

    protected function getCommandTester(): CommandTester
    {
        $this->application->getHelperSet()->set($this->helper, 'question');
        $this->application->addCommand($this->command);

        return new CommandTester($this->command);
    }

    public function testExecute(): void
    {
        $this->helper->method('ask')->willReturn('test@example.com');
        $this->helper->method('ask')->willReturn('A name');
        $this->helper->method('ask')->willReturn('foobar123');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        self::assertEquals('User account created!' . PHP_EOL, $commandTester->getDisplay());
    }
}
