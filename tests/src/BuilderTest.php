<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use Monolog\Logger;
use PHPCensor\Builder;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Configuration;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\CommandExecutor;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildErrorWriter;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class TestBuilder extends Builder
{
    public function getCommandExecutor(): CommandExecutor
    {
        return $this->commandExecutor;
    }
}

class BuilderTest extends TestCase
{
    private BuildLogger $buildLogger;
    private Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration = new Configuration('');

        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $storeRegistry = new StoreRegistry($databaseManager);

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

        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['Test'])
            ->getMock();

        $this->buildLogger = $this->getMockBuilder(BuildLogger::class)
            ->setConstructorArgs([$logger, $build])
            ->getMock();

        $this->builder = $this->getMockBuilder(TestBuilder::class)
            ->setConstructorArgs([$configuration, $databaseManager, $storeRegistry, $build, $this->buildLogger])
            ->onlyMethods(['executeCommand'])
            ->getMock();
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(Builder::class, $this->builder);
    }

    public function testGetBuildLogger(): void
    {
        self::assertInstanceOf(BuildLogger::class, $this->builder->getBuildLogger());
    }

    public function testGetConfiguration(): void
    {
        self::assertInstanceOf(ConfigurationInterface::class, $this->builder->getConfiguration());
        self::assertInstanceOf(Configuration::class, $this->builder->getConfiguration());
    }

    public function testGetCurrentStage(): void
    {
        self::assertNull($this->builder->getCurrentStage());

        $this->builder->execute();

        self::assertEquals(BuildInterface::STAGE_COMPLETE, $this->builder->getCurrentStage());
    }

    public function testGetBuildErrorWriter(): void
    {
        self::assertInstanceOf(BuildErrorWriter::class, $this->builder->getBuildErrorWriter());
    }

    public function testLogExecOutput(): void
    {
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput();
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput(true);
        self::assertTrue($this->builder->getCommandExecutor()->logExecOutput);

        $this->builder->logExecOutput(false);
        self::assertFalse($this->builder->getCommandExecutor()->logExecOutput);
    }

    public function testGetConfig(): void
    {
        self::assertEquals([], $this->builder->getConfig());
        self::assertEquals(null, $this->builder->getConfig('test'));

        $this->builder->setConfig(['test' => 'test-value']);

        self::assertEquals(['test' => 'test-value'], $this->builder->getConfig());
        self::assertEquals('test-value', $this->builder->getConfig('test'));
    }

    public function testLogDebug(): void
    {
        $this->buildLogger
            ->expects($this->once())
            ->method('logDebug')
            ->with('Debug message');

        $this->builder->logDebug('Debug message');
    }

    public function testLogSuccess(): void
    {
        $this->buildLogger
            ->expects($this->once())
            ->method('logSuccess')
            ->with('Success message');

        $this->builder->logSuccess('Success message');
    }

    public function testLogWarning(): void
    {
        $this->buildLogger
            ->expects($this->once())
            ->method('logWarning')
            ->with('Warning message');

        $this->builder->logWarning('Warning message');
    }

    public function testLogFailure(): void
    {
        $this->buildLogger
            ->expects($this->once())
            ->method('logFailure')
            ->with('Failure message', null);

        $this->builder->logFailure('Failure message');
    }

    public function testLog(): void
    {
        $this->buildLogger
            ->expects($this->once())
            ->method('log')
            ->with('Log message', LogLevel::INFO, []);

        $this->builder->log('Log message');
    }
}
