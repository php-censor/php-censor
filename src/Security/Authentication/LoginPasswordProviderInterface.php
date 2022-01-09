<?php

declare(strict_types=1);

namespace PHPCensor\Security\Authentication;

use PHPCensor\Model\User;

/**
 * User provider which authentication using a password.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Adirelle <adirelle@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface LoginPasswordProviderInterface extends UserProviderInterface
{
    /**
     * Verify if the supplied password matches the user's one.
     */
    public function verifyPassword(User $user, string $password): bool;
}
