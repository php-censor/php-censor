<?php

namespace PHPCensor\Plugin\Util;

use PHPUnit\Framework\TestCase;

class BitbucketNotifyPhpUnitResultTest extends TestCase
{
    public function dataProvider()
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
                    'formattedOutput' => "test       | 0\t=> 0",
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
                    'formattedOutput' => "test       | 1\t=> 1\tpls improve me :-(",
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
                    'formattedOutput' => "test       | 0\t=> 1\tgreat success!",
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
                    'formattedOutput' => "test       | 1\t=> 0\t!!!!! o_O",
                    'taskDescription' => 'pls fix test because the coverage has decreased from 1 to 0',
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function testFormating(array $input, array $expected)
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
