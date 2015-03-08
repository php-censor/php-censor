<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication;

use PHPCI\Model\User;

/**
 * User provider which authenticiation using a password.
 *
 * @author   Adirelle <adirelle@gmail.com>
 */
interface LoginPasswordProvider extends UserProvider
{
    /** Verify if the supplied password matches the user's one.
     *
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(User $user, $password);
}
