<?php

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model\Base\User;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct()
    {
        $user = new User($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $user);
        self::assertInstanceOf('PHPCensor\Model\Base\User', $user);

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

    public function testId()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $user->getId());

        $result = $user->setId(100);
        self::assertEquals(false, $result);
    }

    public function testEmail()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setEmail('email@email.com');
        self::assertEquals(true, $result);
        self::assertEquals('email@email.com', $user->getEmail());

        $result = $user->setEmail('email@email.com');
        self::assertEquals(false, $result);
    }

    public function testHash()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setHash('hash');
        self::assertEquals(true, $result);
        self::assertEquals('hash', $user->getHash());

        $result = $user->setHash('hash');
        self::assertEquals(false, $result);
    }

    public function testIsAdmin()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setIsAdmin(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $user->getIsAdmin());

        $result = $user->setIsAdmin(true);
        self::assertEquals(false, $result);
    }

    public function testName()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $user->getName());

        $result = $user->setName('name');
        self::assertEquals(false, $result);
    }

    public function testLanguage()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setLanguage('la');
        self::assertEquals(true, $result);
        self::assertEquals('la', $user->getLanguage());

        $result = $user->setLanguage('la');
        self::assertEquals(false, $result);
    }

    public function testPerPage()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setPerPage(10);
        self::assertEquals(true, $result);
        self::assertEquals(10, $user->getPerPage());

        $result = $user->setPerPage(10);
        self::assertEquals(false, $result);
    }

    public function testProviderKey()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setProviderKey('key');
        self::assertEquals(true, $result);
        self::assertEquals('key', $user->getProviderKey());

        $result = $user->setProviderKey('key');
        self::assertEquals(false, $result);
    }

    public function testProviderData()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setProviderData(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $user->getProviderData());
        self::assertEquals('value-1', $user->getProviderData('key-1'));
        self::assertEquals(null, $user->getProviderData('key-3'));

        $result = $user->setProviderData(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(false, $result);
    }

    public function testRememberKey()
    {
        $user = new User($this->storeRegistry);

        $result = $user->setRememberKey('remember');
        self::assertEquals(true, $result);
        self::assertEquals('remember', $user->getRememberKey());

        $result = $user->setRememberKey('remember');
        self::assertEquals(false, $result);
    }
}
