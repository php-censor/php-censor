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
    public function testSplitChatIdAndTopicId($chatId, $expectedThreadId): void
    {
        $reflection = new \ReflectionClass(TelegramNotify::class);
        $method = $reflection->getMethod('splitChatIdAndTopicId');
        $method->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();

        $result = $method->invoke($instance, $chatId);
        self::assertSame($expectedThreadId, $result);
    }

    public function chatIdProvider(): array
    {
        return [
            'without message thread id' => ['-12345', ['-12345', null]],
            'with message thread id' => ['12345/67890', ['12345', '67890']],
            'empty thread id' => ['12345/', ['12345', null]],
            'not group chat' => ['12345', ['12345', null]],
            'empty input' => ['', ['', null]],
            'only slash' => ['/', ['', null]],
            'double slash' => ['//', ['', null]],
            'group id digits only' => [12345, ['12345', null]],
            'group id digits only (negative)' => [-12345, ['-12345', null]],
            'zero topic id' => ['-12345/0', ['-12345', '0']],
            'spaces' => ['   -12345/0   ', ['-12345', '0']],
        ];
    }
}
