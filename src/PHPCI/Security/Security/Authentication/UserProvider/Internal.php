<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication\UserProvider;

use PHPCI\Model\User;
use PHPCI\Security\Authentication\LoginPasswordProvider;

/**
 * Internal user provider.
 * @author   Adirelle <adirelle@gmail.com>
 */
class Internal extends AbstractProvider implements LoginPasswordProvider
{

    public function verifyPassword(User $user, $password)
    {
        return password_verify($password, $user->getHash());
    }

    public function checkRequirements()
    {
        // Always fine
    }

    public function provisionUser($identifier)
    {
        return null;
    }
}
