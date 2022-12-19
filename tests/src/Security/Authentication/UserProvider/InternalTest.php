<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\DatabaseManager;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\UserProvider\Internal;
use PHPCensor\Store\UserStore;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class InternalTest extends TestCase
{
    private Internal $provider;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $userStore = $this
            ->getMockBuilder(UserStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->provider = new Internal($userStore, 'internal', [
            'type' => 'internal',
        ]);
    }

    public function testVerifyPassword(): void
    {
        $user = new User();
        $password = 'bla';
        $user->setHash(\password_hash($password, PASSWORD_DEFAULT));

        self::assertTrue($this->provider->verifyPassword($user, $password));
    }

    public function testVerifyInvalidPassword(): void
    {
        $user = new User();
        $password = 'foo';
        $user->setHash(\password_hash($password, PASSWORD_DEFAULT));

        self::assertFalse($this->provider->verifyPassword($user, 'bar'));
    }

    public function testProvisionUser(): void
    {
        self::assertNull($this->provider->provisionUser('john@doe.com'));
    }
}
