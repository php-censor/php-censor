<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Plugin\TelegramNotify;
use PHPUnit\Framework\TestCase;

class TelegramNotifyTest extends TestCase
{
    /**
     * @dataProvider chatIdProvider
     */
    public function testGetMessageThreadIdFromGroupId($chatId, $expectedThreadId): void
    {
        $reflection = new \ReflectionClass(TelegramNotify::class);
        $method = $reflection->getMethod('getMessageThreadIdFromGroupId');
        $method->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();

        $result = $method->invoke($instance, $chatId);
        self::assertSame($expectedThreadId, $result);
    }

    public function chatIdProvider(): array
    {
        return [
            'without message thread id' => ['-12345', null],
            'with message thread id' => ['12345/67890', '67890'],
            'empty thread id' => ['12345/', null],
            'not group chat' => ['12345', null],
            'empty input' => ['', null],
            'only slash' => ['/', null],
            'double slash' => ['//', null],
            'group id digits only' => [12345, null],
        ];
    }
}
