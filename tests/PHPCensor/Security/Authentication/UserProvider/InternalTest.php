<?php

namespace Tests\PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\UserProvider\Internal;

class InternalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Internal
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Internal('internal', [
            'type' => 'internal',
        ]);
    }

    public function testVerifyPassword()
    {
        $user = new User();
        $password = 'bla';
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));

        self::assertTrue($this->provider->verifyPassword($user, $password));
    }

    public function testVerifyInvaldPassword()
    {
        $user = new User();
        $password = 'foo';
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));

        self::assertFalse($this->provider->verifyPassword($user, 'bar'));
    }

    public function testCheckRequirements()
    {
        $this->provider->checkRequirements();
    }

    public function testProvisionUser()
    {
        self::assertNull($this->provider->provisionUser('john@doe.com'));
    }
}
