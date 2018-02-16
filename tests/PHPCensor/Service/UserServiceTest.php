<?php

namespace Tests\PHPCensor\Service;

use PHPCensor\Model\User;
use PHPCensor\Service\UserService;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class UserServiceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var UserService $testedService
     */
    protected $testedService;

    /**
     * @var \ $mockBuildStore
     */
    protected $mockUserStore;

    public function setUp()
    {
        $this->mockUserStore = $this->getMockBuilder('PHPCensor\Store\UserStore')->getMock();
        $this->mockUserStore->expects($this->any())
                               ->method('save')
                               ->will($this->returnArgument(0));

        $this->testedService = new UserService($this->mockUserStore);
    }

    public function testExecute_CreateNonAdminUser()
    {
        $user = $this->testedService->createUser(
            'Test',
            'test@example.com',
            'internal',
            json_encode(['type' => 'internal']),
            'testing',
            false
        );

        self::assertEquals('Test', $user->getName());
        self::assertEquals('test@example.com', $user->getEmail());
        self::assertEquals(0, $user->getIsAdmin());
        self::assertTrue(password_verify('testing', $user->getHash()));
    }

    public function testExecute_CreateAdminUser()
    {
        $user = $this->testedService->createUser(
            'Test',
            'test@example.com',
            'internal',
            json_encode(['type' => 'internal']),
            'testing',
            true
        );

        self::assertEquals(1, $user->getIsAdmin());
    }

    public function testExecute_RevokeAdminStatus()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test');
        $user->setIsAdmin(1);

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'testing', 0);
        self::assertEquals(0, $user->getIsAdmin());
    }

    public function testExecute_GrantAdminStatus()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test');
        $user->setIsAdmin(0);

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'testing', 1);
        self::assertEquals(1, $user->getIsAdmin());
    }

    public function testExecute_ChangesPasswordIfNotEmpty()
    {
        $user = new User();
        $user->setHash(password_hash('testing', PASSWORD_DEFAULT));

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'newpassword', 0);
        self::assertFalse(password_verify('testing', $user->getHash()));
        self::assertTrue(password_verify('newpassword', $user->getHash()));
    }

    public function testExecute_DoesNotChangePasswordIfEmpty()
    {
        $user = new User();
        $user->setHash(password_hash('testing', PASSWORD_DEFAULT));

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', '', 0);
        self::assertTrue(password_verify('testing', $user->getHash()));
    }
}
