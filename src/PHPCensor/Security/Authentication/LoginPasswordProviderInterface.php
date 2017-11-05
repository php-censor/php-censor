<?php

namespace PHPCensor\Security\Authentication;

use PHPCensor\Model\User;

/**
 * User provider which authenticiation using a password.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface LoginPasswordProviderInterface extends UserProviderInterface
{
    /**
     * Verify if the supplied password matches the user's one.
     *
     * @param User   $user
     * @param string $password
     *
     * @return boolean
     */
    public function verifyPassword(User $user, $password);
}
