<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Logging;

use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use PHPCensor\Logging\OutputLogHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class TestingOutputLogHandler extends OutputLogHandler
{
    public function publicWrite(LogRecord $record): void
    {
        $this->write($record);
    }
}

class OutputLogHandlerTest extends TestCase
{
    public function testWrite(): void
    {
        $output = $this->createMock(NullOutput::class);
        $output
            ->expects($this->once())
            ->method('write')
            ->with('Formatted message');

        $handler = new TestingOutputLogHandler($output);

        $handler->publicWrite(new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'log',
            level: Level::Info,
            message: 'Message',
            formatted: 'Formatted message',
        ));
    }
}
