<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

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
        $options = array(
            'config' => ROOT_DIR . 'phpunit.xml'
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('runConfigFile'))->getMock();
        $mockPlugin->expects($this->once())->method('runConfigFile')->with(ROOT_DIR . 'phpunit.xml');

        $mockPlugin->execute();
    }

    public function testMultiConfigFile()
    {
        $options = array(
            'config' => array(
                ROOT_DIR . 'phpunit1.xml',
                ROOT_DIR . 'phpunit2.xml',
            )
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('runConfigFile'))->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfigFile')->withConsecutive(
            array(ROOT_DIR . 'phpunit1.xml'), array(ROOT_DIR . 'phpunit2.xml')
        );

        $mockPlugin->execute();
    }



    /**
     * @param array $options
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    protected function getPluginBuilder($options = array())
    {
        $loggerMock = $this->getMockBuilder('\Monolog\Logger')
            ->setConstructorArgs(array('Test'))
            ->setMethods(array('addRecord'))
            ->getMock();

        $mockBuild   = $this->getMockBuilder('\PHPCensor\Model\Build')->getMock();
        $mockBuilder = $this->getMockBuilder('\PHPCensor\Builder')
            ->setConstructorArgs(array($mockBuild, $loggerMock))
            ->setMethods(array('executeCommand'))->getMock();

        return $this->getMockBuilder('PHPCensor\Plugin\PhpUnit')->setConstructorArgs(
            array($mockBuilder, $mockBuild, $options)
        );
    }

    public function testSingleDir()
    {
        $options = array(
            'directory' => '/test/directory/one'
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('runDir'))->getMock();
        $mockPlugin->expects($this->once())->method('runDir')->with('/test/directory/one');

        $mockPlugin->execute();
    }

    public function testMultiDir()
    {
        $options = array(
            'directory' => array(
                '/test/directory/one',
                '/test/directory/two',
            )
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('runDir'))->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runDir')->withConsecutive(
            array('/test/directory/one'), array('/test/directory/two')
        );

        $mockPlugin->execute();
    }

    public function testProcessResultsFromConfig()
    {
        $options = array(
            'config' => ROOT_DIR . 'phpunit.xml'
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('processResults'))->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testProcessResultsFromDir()
    {
        $options = array(
            'directory' => ROOT_DIR . 'Tests'
        );

        $mockPlugin = $this->getPluginBuilder($options)->setMethods(array('processResults'))->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }
}
