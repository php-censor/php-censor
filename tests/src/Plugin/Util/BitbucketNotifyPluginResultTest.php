<?php

namespace PHPCensor\Plugin\Util;

use PHPUnit\Framework\TestCase;

class BitbucketNotifyPluginResultTest extends TestCase
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
                    'test', 1, 0,
                ],
                [
                    'unchanged' => false,
                    'improved' => true,
                    'degraded' => false,
                    'string' => 'test',
                    'formattedOutput' => "test       | 1\t=> 0\tgreat success!",
                    'taskDescription' => '',
                ]
            ],
            'degraded' => [
                [
                    'test', 0, 1,
                ],
                [
                    'unchanged' => false,
                    'improved' => false,
                    'degraded' => true,
                    'string' => 'test',
                    'formattedOutput' => "test       | 0\t=> 1\t!!!!! o_O",
                    'taskDescription' => 'pls fix test because it has increased from 0 to 1 errors',
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFormatting(array $input, array $expected)
    {
        $pluginResult = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['degraded'], $pluginResult->isDegraded());
        $this->assertSame($expected['taskDescription'], $pluginResult->generateTaskDescription());
        $this->assertSame($expected['formattedOutput'], $pluginResult->generateFormattedOutput(10));
        $this->assertEquals($expected['degraded'], $pluginResult->isDegraded());
        $this->assertEquals($expected['improved'], $pluginResult->isImproved());
        $this->assertEquals($expected['unchanged'], $pluginResult->isUnchanged());
    }

    public function testSetterGetter()
    {
        $pluginResult = new BitbucketNotifyPluginResult('noname', 9, 9);
        $this->assertEquals(false, $pluginResult->isDegraded());
        $this->assertEquals(false, $pluginResult->isImproved());
        $this->assertEquals(true, $pluginResult->isUnchanged());

        $pluginResult->setLeft(99);
        $pluginResult->setRight(199);
        $pluginResult->setPlugin('BestPluginEver4Cod1ng');

        $this->assertEquals('99', $pluginResult->getLeft());
        $this->assertEquals(199, $pluginResult->getRight());
        $this->assertEquals('BestPluginEver4Cod1ng', $pluginResult->getPlugin());
        $this->assertEquals(true, $pluginResult->isDegraded());
        $this->assertEquals(false, $pluginResult->isImproved());
        $this->assertEquals(false, $pluginResult->isUnchanged());
    }
}
