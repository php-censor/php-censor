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
     * @return boolean
     */
    public function verifyPassword(User $user, $password)
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
    public function provisionUser($identifier)
    {
        return null;
    }
}
