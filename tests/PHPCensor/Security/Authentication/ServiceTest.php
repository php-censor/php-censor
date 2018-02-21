<?php

namespace Tests\PHPCensor\Security\Authentication;

use PHPCensor\Security\Authentication\Service;

class ServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetInstance()
    {
        self::assertInstanceOf('\PHPCensor\Security\Authentication\Service', Service::getInstance());
    }

    public function testBuildBuiltinProvider()
    {
        $provider = Service::buildProvider('test', ['type' => 'internal']);

        self::assertInstanceOf('\PHPCensor\Security\Authentication\UserProvider\Internal', $provider);
    }

    public function testBuildAnyProvider()
    {
        $config   = ['type' => '\Tests\PHPCensor\Security\Authentication\DummyProvider'];
        $provider = Service::buildProvider("test", $config);

        self::assertInstanceOf('\Tests\PHPCensor\Security\Authentication\DummyProvider', $provider);
        self::assertEquals('test', $provider->key);
        self::assertEquals($config, $provider->config);
    }

    public function testGetProviders()
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($providers);

        self::assertEquals($providers, $service->getProviders());
    }

    public function testGetLoginPasswordProviders()
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\LoginPasswordProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($providers);

        self::assertEquals(['b' => $b], $service->getLoginPasswordProviders());
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
