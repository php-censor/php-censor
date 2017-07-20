<?php

namespace Tests\PHPCensor\Plugin;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleConfigFile()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->once())->method('runConfig')->with(null, ROOT_DIR . 'phpunit.xml');

        $mockPlugin->execute();
    }

    public function testMultiConfigFile()
    {
        $options = [
            'config' => [
                ROOT_DIR . 'phpunit1.xml',
                ROOT_DIR . 'phpunit2.xml',
            ]
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            [null, ROOT_DIR . 'phpunit1.xml'],
            [null, ROOT_DIR . 'phpunit2.xml']
        );

        $mockPlugin->execute();
    }



    /**
     * @param array $options
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    protected function getPluginBuilder($options = [])
    {
        $loggerMock = $this->getMockBuilder('\Monolog\Logger')
            ->setConstructorArgs(['Test'])
            ->setMethods(['addRecord'])
            ->getMock();

        $mockBuild   = $this->getMockBuilder('\PHPCensor\Model\Build')->getMock();
        $mockBuilder = $this->getMockBuilder('\PHPCensor\Builder')
            ->setConstructorArgs([$mockBuild, $loggerMock])
            ->setMethods(['executeCommand'])->getMock();

        return $this->getMockBuilder('PHPCensor\Plugin\PhpUnit')->setConstructorArgs(
            [$mockBuilder, $mockBuild, $options]
        );
    }

    public function testSingleDir()
    {
        $options = [
            'directory' => '/test/directory/one'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->once())->method('runConfig')->with('/test/directory/one', null);

        $mockPlugin->execute();
    }

    public function testMultiDir()
    {
        $options = [
            'directory' => [
                '/test/directory/one',
                '/test/directory/two',
            ]
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            ['/test/directory/one'], ['/test/directory/two']
        );

        $mockPlugin->execute();
    }

    public function testProcessResultsFromConfig()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testProcessResultsFromDir()
    {
        $options = [
            'directory' => ROOT_DIR . 'Tests'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }
}
