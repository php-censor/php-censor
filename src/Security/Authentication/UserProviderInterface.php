<?php

declare(strict_types = 1);

namespace PHPCensor\Security\Authentication;

use Exception;
use PHPCensor\Model\User;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Adirelle <adirelle@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface UserProviderInterface
{
    /**
     * Check if all software requirements are met (libraries, extensions, ...)
     *
     * @throws Exception
     */
    public function checkRequirements(): void;

    /**
     * Provision an new user for the given identifier.
     *
     * @param string|null $identifier The user identifier.
     *
     * @return User|null The new user or null if the provider does not know the user.
     */
    public function provisionUser(?string $identifier): ?User;
}
