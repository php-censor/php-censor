<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright Copyright 2014, Block 8 Limited.
 * @license   https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link      https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\UserProvider\Internal;

class InternalTest extends \PHPUnit_Framework_TestCase
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

        $this->assertTrue($this->provider->verifyPassword($user, $password));
    }

    public function testVerifyInvaldPassword()
    {
        $user = new User();
        $password = 'foo';
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));

        $this->assertFalse($this->provider->verifyPassword($user, 'bar'));
    }

    public function testCheckRequirements()
    {
        $this->provider->checkRequirements();
    }

    public function testProvisionUser()
    {
        $this->assertNull($this->provider->provisionUser('john@doe.com'));
    }
}
