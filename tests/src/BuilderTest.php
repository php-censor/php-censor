<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use Monolog\Logger;
use PHPCensor\Builder;
use PHPCensor\Configuration;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\CommandExecutor;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildErrorWriter;
use PHPCensor\Store\BuildStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class TestBuilder extends Builder
{
    public function getCommandExecutor(): CommandExecutor
    {
        return $this->commandExecutor;
    }
}

class BuilderTest extends TestCase
{
    private Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['Test'])
            ->onlyMethods(['addRecord'])
            ->getMock();

        $configuration = new Configuration('');

        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();

        $storeRegistry
            ->method('get')
            ->with('Build')
            ->willReturn($buildStore);

        $project = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $build = $this
            ->getMockBuilder(Build::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $build
            ->method('getId')
            ->willReturn(10);

        $build
            ->method('getProjectId')
            ->willReturn(20);

        $build
            ->method('getBranch')
            ->willReturn('master');

        $build
            ->method('getProject')
            ->willReturn($project);

        $this->builder = $this->getMockBuilder(TestBuilder::class)
            ->setConstructorArgs([$configuration, $databaseManager, $storeRegistry, $build, $logger])
            ->onlyMethods(['executeCommand'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testConstruct()
    {
        self::assertInstanceOf(Builder::class, $this->builder);
    }

    public function testGetBuildLogger()
    {
        //self::assertInstanceOf(BuildLoggerInterface::class, $this->builder->getBuildLogger());
        self::assertInstanceOf(BuildLogger::class, $this->builder->getBuildLogger());
    }

    public function testGetConfiguration()
    {
        self::assertInstanceOf(ConfigurationInterface::class, $this->builder->getConfiguration());
        self::assertInstanceOf(Configuration::class, $this->builder->getConfiguration());
    }

    public function testGetCurrentStage()
    {
        self::assertNull($this->builder->getCurrentStage());

        $this->builder->execute();

        self::assertEquals(Build::STAGE_COMPLETE, $this->builder->getCurrentStage());
    }

    public function testGetBuildErrorWriter()
    {
        self::assertInstanceOf(BuildErrorWriter::class, $this->builder->getBuildErrorWriter());
    }

    public function testLogExecOutput()
    {
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput();
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput(true);
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput(false);
        self::assertFalse($this->builder->getCommandExecutor()->logExecOutput);
    }

    public function testGetConfig()
    {
        self::assertEquals([], $this->builder->getConfig());
        self::assertEquals(null, $this->builder->getConfig('test'));

        $this->builder->setConfig(['test' => 'test-value']);

        self::assertEquals(['test' => 'test-value'], $this->builder->getConfig());
        self::assertEquals('test-value', $this->builder->getConfig('test'));
    }
}
