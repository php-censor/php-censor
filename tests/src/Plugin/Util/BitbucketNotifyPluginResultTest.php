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
     * @param array $input
     * @param array $expected
     */
    public function testIsUnchanged(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['unchanged'], $sut->isUnchanged());
    }


    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function testIsImproved(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['improved'], $sut->isImproved());
    }

    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function testIsDegraded(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['degraded'], $sut->isDegraded());
    }

    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function test__toString(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertEquals($expected['string'], $sut->__toString());
    }

    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function testGenerateFormatedOutput(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertSame($expected['formattedOutput'], $sut->generateFormatedOutput(10));
    }

    /**
     * @dataProvider dataProvider
     * @param array $input
     * @param array $expected
     */
    public function testGenerateTaskDescription(array $input, array $expected)
    {
        $sut = new BitbucketNotifyPluginResult($input[0], $input[1], $input[2]);
        $this->assertSame($expected['taskDescription'], $sut->generateTaskDescription());
    }
}
