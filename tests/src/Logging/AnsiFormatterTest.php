<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Logging;

use PHPCensor\Logging\AnsiFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use DateTimeImmutable;

class AnsiFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $formatter = new AnsiFormatter(null, 'Y-m-d');
        $message   = $formatter->format([
            'level_name' => \strtoupper(LogLevel::WARNING),
            'channel'    => 'log',
            'context'    => [],
            'message'    => "\033[0;31mPart1\033[0m, \033[0;32mPart2\033[0m, \033[0;36mPart3\033[0m",
            'datetime'   => new DateTimeImmutable(),
            'extra'      => [],
        ]);

        $this->assertEquals(
            '[' . \date('Y-m-d') . '] log.WARNING: Part1, Part2, Part3 [] []'."\n",
            $message
        );
    }
}
