<?php

namespace PHPCensor\Security\Authentication;

use PHPCensor\Model\User;

/**
 * User provider interface.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface UserProviderInterface
{

    /**
     * Check if all software requirements are met (libraries, extensions, ...)
     *
     * @throws \Exception
     */
    public function checkRequirements();

    /**
     * Provision an new user for the given identifier.
     *
     * @param string $identifier The user identifier.
     *
     * @return User|null The new user or null if the provider does not know the user.
     */
    public function provisionUser($identifier);
}
