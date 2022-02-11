<?php

namespace Tests\PHPCensor\Logging;

use Exception;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LogLevel;

class BuildLoggerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var BuildLogger
     */
    protected $testedBuildLogger;

    protected $mockLogger;

    protected $mockBuild;

    protected $mockRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockLogger = $this->prophesize('\Psr\Log\LoggerInterface');
        $this->mockRegistry = $this->prophesize('\PHPCensor\StoreRegistry');
        $this->mockBuild = $this->prophesize('\PHPCensor\Model\Build')
            ->willBeConstructedWith([$this->mockRegistry->reveal(), ['id' => 1]]);

        $this->testedBuildLogger = new BuildLogger(
            $this->mockLogger->reveal(),
            $this->mockBuild->reveal()
        );
    }

    public function testLog_CallsWrappedLogger()
    {
        $level = LogLevel::NOTICE;
        $message   = "Testing";
        $contextIn = [];

        $this->mockLogger
            ->log($level, $message, Argument::type('array'))
            ->shouldBeCalledTimes(1);

        $this->testedBuildLogger->log($message, $level, $contextIn);
    }

    public function testLog_CallsWrappedLoggerForEachMessage()
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

    public function testLogFailure_AddsExceptionContext()
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
