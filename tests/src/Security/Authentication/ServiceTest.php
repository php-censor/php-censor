<?php

namespace Tests\PHPCensor\Security\Authentication;

use PHPCensor\ConfigurationInterface;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\Service;
use PHPCensor\Security\Authentication\UserProvider\AbstractProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ServiceTest extends TestCase
{
    use ProphecyTrait;

    protected ConfigurationInterface $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
    }

    public function testBuildBuiltinProvider()
    {
        $provider = Service::buildProvider('test', ['type' => 'internal']);

        self::assertInstanceOf('\PHPCensor\Security\Authentication\UserProvider\Internal', $provider);
    }

    public function testBuildAnyProvider()
    {
        $config   = ['type' => '\Tests\PHPCensor\Security\Authentication\DummyProvider'];
        $provider = Service::buildProvider('test', $config);

        self::assertInstanceOf('\Tests\PHPCensor\Security\Authentication\DummyProvider', $provider);
        self::assertEquals('test', $provider->getKey());
        self::assertEquals($config, $provider->getConfig());
    }

    public function testGetProviders()
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $providers);

        self::assertEquals($providers, $service->getProviders());
    }

    public function testGetLoginPasswordProviders()
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\LoginPasswordProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $providers);

        self::assertEquals(['b' => $b], $service->getLoginPasswordProviders());
    }
}

class DummyProvider extends AbstractProvider
{
    public function checkRequirements()
    {
    }

    public function provisionUser(?string $identifier): ?User
    {
        return null;
    }
}
