<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Security\Authentication;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;
use PHPCensor\Security\Authentication\Service;
use PHPCensor\Security\Authentication\UserProvider\AbstractProvider;
use PHPCensor\Security\Authentication\UserProvider\Internal;
use PHPCensor\Security\Authentication\UserProviderInterface;
use PHPCensor\Store\UserStore;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ServiceTest extends TestCase
{
    use ProphecyTrait;

    private ConfigurationInterface $configuration;
    protected UserStore $userStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager     = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $this->userStore = $this
            ->getMockBuilder(UserStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testBuildBuiltinProvider(): void
    {
        $provider = Service::buildProvider($this->userStore, 'test', ['type' => 'internal']);

        self::assertInstanceOf(Internal::class, $provider);
    }

    public function testBuildAnyProvider(): void
    {
        $config   = ['type' => DummyProvider::class];
        $provider = Service::buildProvider($this->userStore, 'test', $config);

        self::assertInstanceOf(DummyProvider::class, $provider);
        self::assertEquals('test', $provider->getKey());
        self::assertEquals($config, $provider->getConfig());
    }

    public function testGetProviders(): void
    {
        $a         = $this->prophesize(UserProviderInterface::class)->reveal();
        $b         = $this->prophesize(UserProviderInterface::class)->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $this->userStore, $providers);

        self::assertEquals($providers, $service->getProviders());
    }

    public function testGetLoginPasswordProviders(): void
    {
        $a         = $this->prophesize(UserProviderInterface::class)->reveal();
        $b         = $this->prophesize(LoginPasswordProviderInterface::class)->reveal();
        $providers = ['a' => $a, 'b' => $b];

        $service = new Service($this->configuration, $this->userStore, $providers);

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
