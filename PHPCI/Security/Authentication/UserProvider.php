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
 * User provider interface.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface UserProvider
{

    /** Check if all software requirements are met (libraries, extensions, ...)
     *
     * @throws Exception
     */
    public function checkRequirements();

    /** Provision an new user for the given identifier.
     *
     * @param string $identifier The user identifier.
     *
     * @return User|null The new user or null if the provider does not know the user.
     */
    public function provisionUser($identifier);
}
