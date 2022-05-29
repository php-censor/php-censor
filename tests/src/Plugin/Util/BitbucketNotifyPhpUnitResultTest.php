<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\BitbucketNotifyPhpUnitResult;
use PHPUnit\Framework\TestCase;

class BitbucketNotifyPhpUnitResultTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'unchanged' => [
                [
                    'test', 0, 0,
                ],
                [
                    'unchanged' => true,
                    'improved' => false,
                    'degraded' => false,
                    'string' => 'test',
                    'formattedOutput' => "test       | 0.00\t=> 0.00",
                    'taskDescription' => '',
                ]
            ],
            'unchangedWithFail' => [
                [
                    'test', 1, 1,
                ],
                [
                    'unchanged' => true,
                    'improved' => false,
                    'degraded' => false,
                    'string' => 'test',
                    'formattedOutput' => "test       | 1.00\t=> 1.00\tpls improve me :-(",
                    'taskDescription' => '',
                ]
            ],
            'improved' => [
                [
                    'test', 0, 1,
                ],
                [
                    'unchanged' => false,
                    'improved' => true,
                    'degraded' => false,
                    'string' => 'test',
                    'formattedOutput' => "test       | 0.00\t=> 1.00\tgreat success!",
                    'taskDescription' => '',
                ]
            ],
            'degraded' => [
                [
                    'test', 1, 0,
                ],
                [
                    'unchanged' => false,
                    'improved' => false,
                    'degraded' => true,
                    'string' => 'test',
                    'formattedOutput' => "test       | 1.00\t=> 0.00\t!!!!! o_O",
                    'taskDescription' => 'pls fix test because the coverage has decreased from 1 to 0',
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFormatting(array $input, array $expected): void
    {
        $pluginResult = new BitbucketNotifyPhpUnitResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['degraded'], $pluginResult->isDegraded());
        $this->assertSame($expected['taskDescription'], $pluginResult->generateTaskDescription());
        $this->assertSame($expected['formattedOutput'], $pluginResult->generateFormattedOutput(10));
        $this->assertEquals($expected['degraded'], $pluginResult->isDegraded());
        $this->assertEquals($expected['improved'], $pluginResult->isImproved());
        $this->assertEquals($expected['unchanged'], $pluginResult->isUnchanged());
    }
}
