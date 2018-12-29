<?php

namespace Tests\PHPCensor\Plugin;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockBuilder;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitTest extends TestCase
{
    public function testSingleConfigFile()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->once())->method('runConfig')->with(null, ROOT_DIR . 'phpunit.xml.dist');

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
     * @return PHPUnit_Framework_MockObject_MockBuilder
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
            'directories' => '/test/directory/one'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->once())->method('runConfig')->with('/test/directory/one', null);

        $mockPlugin->execute();
    }

    public function testMultiDir()
    {
        $options = [
            'directories' => [
                '/test/directory/one',
                '/test/directory/two',
            ]
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            ['/test/directory/one'],
            ['/test/directory/two']
        );

        $mockPlugin->execute();
    }

    public function testProcessResultsFromConfig()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testProcessResultsFromDir()
    {
        $options = [
            'directories' => ROOT_DIR . 'Tests'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testRequiredCoverageWithPassingPercentage()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist',
            'coverage' => true,
            'required_lines_coverage' => 60,
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['extractCoverage'])->getMock();
        $mockPlugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '100.00',
            'methods' => '100.00',
            'lines'   => '100.00',
        ]);
        $this->assertTrue($mockPlugin->execute());
    }

    public function testRequiredCoverageWithFailingPercentage()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist',
            'coverage' => true,
            'required_lines_coverage' => 60,
        ];

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(['extractCoverage'])->getMock();
        $mockPlugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '30.00',
            'methods' => '30.00',
            'lines'   => '30.00',
        ]);
        $this->assertFalse($mockPlugin->execute());
    }
}
