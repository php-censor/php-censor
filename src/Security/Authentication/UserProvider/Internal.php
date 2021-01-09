<?php

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;

/**
 * Internal user provider
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Internal extends AbstractProvider implements LoginPasswordProviderInterface
{
    /**
     * @param User   $user
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getHash());
    }

    public function checkRequirements()
    {
        // Always fine
    }

    /**
     * @param string $identifier
     *
     * @return null
     */
    public function provisionUser(?string $identifier): ?User
    {
        return null;
    }
}
