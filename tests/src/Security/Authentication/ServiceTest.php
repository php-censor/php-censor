<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Security\Authentication;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\Service;
use PHPCensor\Security\Authentication\UserProvider\AbstractProvider;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ServiceTest extends TestCase
{
    use ProphecyTrait;

    private ConfigurationInterface $configuration;
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager     = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testBuildBuiltinProvider(): void
    {
        $provider = Service::buildProvider($this->storeRegistry, 'test', ['type' => 'internal']);

        self::assertInstanceOf('\PHPCensor\Security\Authentication\UserProvider\Internal', $provider);
    }

    public function testBuildAnyProvider(): void
    {
        $config   = ['type' => '\Tests\PHPCensor\Security\Authentication\DummyProvider'];
        $provider = Service::buildProvider($this->storeRegistry, 'test', $config);

        self::assertInstanceOf('\Tests\PHPCensor\Security\Authentication\DummyProvider', $provider);
        self::assertEquals('test', $provider->getKey());
        self::assertEquals($config, $provider->getConfig());
    }

    public function testGetProviders(): void
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $this->storeRegistry, $providers);

        self::assertEquals($providers, $service->getProviders());
    }

    public function testGetLoginPasswordProviders(): void
    {
        $a         = $this->prophesize('\PHPCensor\Security\Authentication\UserProviderInterface')->reveal();
        $b         = $this->prophesize('\PHPCensor\Security\Authentication\LoginPasswordProviderInterface')->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $this->storeRegistry, $providers);

        self::assertEquals(['b' => $b], $service->getLoginPasswordProviders());
    }
}

class DummyProvider extends AbstractProvider
{
    public function checkRequirements(): void
    {
    }

    public function provisionUser(?string $identifier): ?User
    {
        return null;
    }
}
