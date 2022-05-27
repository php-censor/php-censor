<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Logging;

use Exception;
use PHPCensor\Logging\BuildLogger;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LogLevel;

class BuildLoggerTest extends TestCase
{
    use ProphecyTrait;

    private BuildLogger $testedBuildLogger;
    private $mockLogger;
    private $mockBuild;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = $this->prophesize('\Psr\Log\LoggerInterface');
        $this->mockBuild = $this->prophesize('\PHPCensor\Model\Build');

        $this->testedBuildLogger = new BuildLogger(
            $this->mockLogger->reveal(),
            $this->mockBuild->reveal()
        );
    }

    public function testLog_CallsWrappedLogger(): void
    {
        $level = LogLevel::NOTICE;
        $message   = "Testing";
        $contextIn = [];

        $this->mockLogger
            ->log($level, $message, Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $this->testedBuildLogger->log($message, $level, $contextIn);
    }

    public function testLog_CallsWrappedLoggerForEachMessage(): void
    {
        $level     = LogLevel::NOTICE;
        $message   = ["One", "Two", "Three"];
        $contextIn = [];

        $this->mockLogger
            ->log($level, "One", Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $this->mockLogger
            ->log($level, "Two", Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $this->mockLogger
            ->log($level, "Three", Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $this->testedBuildLogger->log($message, $level, $contextIn);
    }

    public function testLogFailure_AddsExceptionContext(): void
    {
        $message = "Testing";

        $exception = new Exception("Expected Exception");


        $this->mockLogger
            ->log(
                Argument::type('string'),
                Argument::type('string'),
                Argument::withEntry('exception', $exception)
            )
            ->shouldBeCalledTimes(1);

        $this->testedBuildLogger->logFailure($message, $exception);
    }
}
