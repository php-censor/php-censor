<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication\Tests;

use PHPCI\Security\Authentication\Service;
use PHPUnit_Framework_TestCase;

class ServiceTest extends \Prophecy\PhpUnit\ProphecyTestCase
{
    /**
     * @covers PHPCI\Security\Authentication\Service::getInstance
     * @todo   Implement testGetInstance().
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('PHPCI\Security\Authentication\Service', Service::getInstance());
    }

    /**
     * @covers PHPCI\Security\Authentication\Service::buildProvider
     */
    public function testBuildBuiltinProvider()
    {
        $provider = Service::buildProvider("test", array('type' => 'internal'));

        $this->assertInstanceOf('PHPCI\Security\Authentication\UserProvider\Internal', $provider);
    }

    /**
     * @covers PHPCI\Security\Authentication\Service::buildProvider
     */
    public function testBuildAnyProvider()
    {
        $config = array('type' => 'PHPCI\Security\Authentication\Tests\DummyProvider');
        $provider = Service::buildProvider("test", $config);

        $this->assertInstanceOf('PHPCI\Security\Authentication\Tests\DummyProvider', $provider);
        $this->assertEquals('test', $provider->key);
        $this->assertEquals($config, $provider->config);
    }

    /**
     * @covers PHPCI\Security\Authentication\Service::getProviders
     */
    public function testGetProviders()
    {
        $a = $this->prophesize('PHPCI\Security\Authentication\UserProvider')->reveal();
        $b = $this->prophesize('PHPCI\Security\Authentication\UserProvider')->reveal();
        $providers = array('a' => $a, 'b' => $b);

        $service = new Service($providers);

        $this->assertEquals($providers, $service->getProviders());
    }

    /**
     * @covers PHPCI\Security\Authentication\Service::getLoginPasswordProviders
     * @todo   Implement testGetLoginPasswordProviders().
     */
    public function testGetLoginPasswordProviders()
    {
        $a = $this->prophesize('PHPCI\Security\Authentication\UserProvider')->reveal();
        $b = $this->prophesize('PHPCI\Security\Authentication\LoginPasswordProvider')->reveal();
        $providers = array('a' => $a, 'b' => $b);

        $service = new Service($providers);

        $this->assertEquals(array('b' => $b), $service->getLoginPasswordProviders());
    }
}

class DummyProvider
{
    public $key;
    public $config;
    public function __construct($key, array $config)
    {
        $this->key = $key;
        $this->config = $config;
    }
}
