<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Service;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\User;
use PHPCensor\Service\UserService;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class UserServiceTest extends TestCase
{
    private UserService $testedService;
    private UserStore $mockUserStore;
    private ConfigurationInterface $configuration;
    private DatabaseManager $databaseManager;
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $this->configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $this->databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $this->mockUserStore = $this
            ->getMockBuilder('PHPCensor\Store\UserStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $this->mockUserStore
            ->expects($this->any())
            ->method('save')
            ->will($this->returnArgument(0));

        $this->testedService = new UserService($this->storeRegistry, $this->mockUserStore);
    }

    public function testExecute_CreateNonAdminUser(): void
    {
        $user = $this->testedService->createUser(
            'Test',
            'test@example.com',
            'internal',
            ['type' => 'internal'],
            'testing',
            false
        );

        self::assertEquals('Test', $user->getName());
        self::assertEquals('test@example.com', $user->getEmail());
        self::assertEquals(false, $user->getIsAdmin());
        self::assertTrue(password_verify('testing', $user->getHash()));
    }

    public function testExecute_CreateAdminUser(): void
    {
        $user = $this->testedService->createUser(
            'Test',
            'test@example.com',
            'internal',
            ['type' => 'internal'],
            'testing',
            true
        );

        self::assertEquals(true, $user->getIsAdmin());
    }

    public function testExecute_RevokeAdminStatus(): void
    {
        $user = new User($this->storeRegistry);
        $user->setEmail('test@example.com');
        $user->setName('Test');
        $user->setIsAdmin(true);

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'testing', false);
        self::assertEquals(false, $user->getIsAdmin());
    }

    public function testExecute_GrantAdminStatus(): void
    {
        $user = new User($this->storeRegistry);
        $user->setEmail('test@example.com');
        $user->setName('Test');
        $user->setIsAdmin(false);

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'testing', true);
        self::assertEquals(true, $user->getIsAdmin());
    }

    public function testExecute_ChangesPasswordIfNotEmpty(): void
    {
        $user = new User($this->storeRegistry);
        $user->setHash(password_hash('testing', PASSWORD_DEFAULT));

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', 'newpassword', false);
        self::assertFalse(password_verify('testing', $user->getHash()));
        self::assertTrue(password_verify('newpassword', $user->getHash()));
    }

    public function testExecute_DoesNotChangePasswordIfEmpty(): void
    {
        $user = new User($this->storeRegistry);
        $user->setHash(password_hash('testing', PASSWORD_DEFAULT));

        $user = $this->testedService->updateUser($user, 'Test', 'test@example.com', '', false);
        self::assertTrue(password_verify('testing', $user->getHash()));
    }

    public function testExecuteDeleteUser(): void
    {
        $store = $this
            ->getMockBuilder('PHPCensor\Store\UserStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $service = new UserService($this->storeRegistry, $store);
        $user = new User($this->storeRegistry);

        self::assertEquals(false, $service->deleteUser($user));

        $user->setId(11);
        self::assertEquals(true, $service->deleteUser($user));
    }
}
