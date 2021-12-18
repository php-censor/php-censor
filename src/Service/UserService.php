<?php

namespace PHPCensor\Service;

use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;

/**
 * The user service handles the creation, modification and deletion of users.
 */
class UserService
{
    /**
     * @var UserStore
     */
    protected $store;

    public function __construct(UserStore $store)
    {
        $this->store = $store;
    }

    /**
     * Create a new user.
     *
     * @param string  $name
     * @param string  $email
     * @param string  $providerKey
     * @param array   $providerData
     * @param string  $password
     * @param bool $isAdmin
     *
     * @return User
     */
    public function createUser($name, $email, $providerKey, $providerData, $password, $isAdmin = false)
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));
        $user->setProviderKey($providerKey);
        $user->setProviderData($providerData);
        $user->setIsAdmin($isAdmin);

        return $this->store->save($user);
    }

    /**
     * Update a user.
     *
     * @param string $name
     * @param string $emailAddress
     * @param string $password
     * @param bool   $isAdmin
     * @param string $language
     * @param int    $perPage
     *
     * @return User
     */
    public function updateUser(User $user, $name, $emailAddress, $password = null, $isAdmin = null, $language = null, $perPage = null)
    {
        $user->setName($name);
        $user->setEmail($emailAddress);

        if (!empty($password)) {
            $user->setHash(password_hash($password, PASSWORD_DEFAULT));
        }

        if (!is_null($isAdmin)) {
            $user->setIsAdmin($isAdmin);
        }

        $user->setLanguage($language);
        $user->setPerPage($perPage);

        return $this->store->save($user);
    }

    /**
     * Delete a user.
     *
     * @return bool
     */
    public function deleteUser(User $user)
    {
        return $this->store->delete($user);
    }
}
