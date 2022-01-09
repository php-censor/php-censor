<?php

declare(strict_types = 1);

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Adirelle <adirelle@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Internal extends AbstractProvider implements LoginPasswordProviderInterface
{
    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getHash());
    }

    public function checkRequirements(): void
    {
        // Always fine
    }

    /**
     * @param string|null $identifier
     *
     * @return User|null
     */
    public function provisionUser(?string $identifier): ?User
    {
        return null;
    }
}
