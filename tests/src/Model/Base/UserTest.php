<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\User;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class UserTest extends TestCase
{
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct(): void
    {
        $user = new User();

        self::assertInstanceOf(Model::class, $user);
        self::assertInstanceOf(User::class, $user);

        self::assertEquals([
            'id'            => null,
            'email'         => null,
            'hash'          => null,
            'is_admin'      => 0,
            'name'          => null,
            'language'      => null,
            'per_page'      => null,
            'provider_key'  => 'internal',
            'provider_data' => null,
            'remember_key'  => null,
        ], $user->getDataArray());
    }

    public function testId(): void
    {
        $user = new User();

        $result = $user->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $user->getId());

        $result = $user->setId(100);
        self::assertEquals(false, $result);
    }

    public function testEmail(): void
    {
        $user = new User();

        $result = $user->setEmail('email@email.com');
        self::assertEquals(true, $result);
        self::assertEquals('email@email.com', $user->getEmail());

        $result = $user->setEmail('email@email.com');
        self::assertEquals(false, $result);
    }

    public function testHash(): void
    {
        $user = new User();

        $result = $user->setHash('hash');
        self::assertEquals(true, $result);
        self::assertEquals('hash', $user->getHash());

        $result = $user->setHash('hash');
        self::assertEquals(false, $result);
    }

    public function testIsAdmin(): void
    {
        $user = new User();

        $result = $user->setIsAdmin(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $user->getIsAdmin());

        $result = $user->setIsAdmin(true);
        self::assertEquals(false, $result);
    }

    public function testName(): void
    {
        $user = new User();

        $result = $user->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $user->getName());

        $result = $user->setName('name');
        self::assertEquals(false, $result);
    }

    public function testLanguage(): void
    {
        $user = new User();

        $result = $user->setLanguage('la');
        self::assertEquals(true, $result);
        self::assertEquals('la', $user->getLanguage());

        $result = $user->setLanguage('la');
        self::assertEquals(false, $result);
    }

    public function testPerPage(): void
    {
        $user = new User();

        $result = $user->setPerPage(10);
        self::assertEquals(true, $result);
        self::assertEquals(10, $user->getPerPage());

        $result = $user->setPerPage(10);
        self::assertEquals(false, $result);
    }

    public function testProviderKey(): void
    {
        $user = new User();

        $result = $user->setProviderKey('key');
        self::assertEquals(true, $result);
        self::assertEquals('key', $user->getProviderKey());

        $result = $user->setProviderKey('key');
        self::assertEquals(false, $result);
    }

    public function testProviderData(): void
    {
        $user = new User();

        $result = $user->setProviderData(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $user->getProviderData());
        self::assertEquals('value-1', $user->getProviderData('key-1'));
        self::assertEquals(null, $user->getProviderData('key-3'));

        $result = $user->setProviderData(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(false, $result);
    }

    public function testRememberKey(): void
    {
        $user = new User();

        $result = $user->setRememberKey('remember');
        self::assertEquals(true, $result);
        self::assertEquals('remember', $user->getRememberKey());

        $result = $user->setRememberKey('remember');
        self::assertEquals(false, $result);
    }
}
