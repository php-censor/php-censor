<?php

declare(strict_types=1);

namespace PHPCensor\Service;

use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;

/**
 * The user service handles the creation, modification and deletion of users.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class UserService
{
    private UserStore $store;

    private StoreRegistry $storeRegistry;

    public function __construct(
        StoreRegistry $storeRegistry,
        UserStore $store
    ) {
        $this->storeRegistry = $storeRegistry;
        $this->store         = $store;
    }

    /**
     * Create a new user.
     *
     * @return User
     */
    public function createUser(string $name, string $email, string $providerKey, array $providerData, string $password, bool $isAdmin = false): ?User
    {
        $user = new User($this->storeRegistry);
        $user->setName($name);
        $user->setEmail($email);
        $user->setHash(\password_hash($password, PASSWORD_DEFAULT));
        $user->setProviderKey($providerKey);
        $user->setProviderData($providerData);
        $user->setIsAdmin($isAdmin);

        return $this->store->save($user);
    }

    /**
     * Update a user.
     *
     * @param string $password
     * @param bool   $isAdmin
     * @param string $language
     * @param int    $perPage
     *
     * @return User
     */
    public function updateUser(User $user, string $name, string $emailAddress, ?string $password = null, ?bool $isAdmin = null, ?string $language = null, ?int $perPage = null): ?User
    {
        $user->setName($name);
        $user->setEmail($emailAddress);

        if (!empty($password)) {
            $user->setHash(\password_hash($password, PASSWORD_DEFAULT));
        }

        if (!\is_null($isAdmin)) {
            $user->setIsAdmin($isAdmin);
        }

        $user->setLanguage($language);
        $user->setPerPage($perPage);

        return $this->store->save($user);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): bool
    {
        return $this->store->delete($user);
    }
}
