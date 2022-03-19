<?php

namespace Tests\PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\UserProvider\Internal;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class InternalTest extends TestCase
{
    /**
     * @var Internal
     */
    protected $provider;

    protected StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->provider = new Internal($this->storeRegistry, 'internal', [
            'type' => 'internal',
        ]);
    }

    public function testVerifyPassword()
    {
        $user = new User($this->storeRegistry);
        $password = 'bla';
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));

        self::assertTrue($this->provider->verifyPassword($user, $password));
    }

    public function testVerifyInvaldPassword()
    {
        $user = new User($this->storeRegistry);
        $password = 'foo';
        $user->setHash(\password_hash($password, PASSWORD_DEFAULT));

        self::assertFalse($this->provider->verifyPassword($user, 'bar'));
    }

    public function testProvisionUser()
    {
        self::assertNull($this->provider->provisionUser('john@doe.com'));
    }
}
