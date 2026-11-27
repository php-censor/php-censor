<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Logging;

use Monolog\Level;
use Monolog\LogRecord;
use PHPCensor\Logging\AnsiFormatter;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class AnsiFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $formatter = new AnsiFormatter(null, 'Y-m-d');
        $message   = $formatter->format(new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'log',
            level: Level::Warning,
            message: "\033[0;31mPart1\033[0m, \033[0;32mPart2\033[0m, \033[0;36mPart3\033[0m",
        ));

        $this->assertEquals(
            '[' . \date('Y-m-d') . '] log.WARNING: Part1, Part2, Part3 [] []'."\n",
            $message
        );
    }
}
