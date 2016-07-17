<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Service;

use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;

/**
 * The user service handles the creation, modification and deletion of users.
 * Class UserService
 * @package PHPCensor\Service
 */
class UserService
{
    /**
     * @var \PHPCensor\Store\UserStore
     */
    protected $store;

    /**
     * @param UserStore $store
     */
    public function __construct(UserStore $store)
    {
        $this->store = $store;
    }

    /**
     * Create a new user within PHPCI.
     * 
     * @param string  $name
     * @param string  $emailAddress
     * @param string  $password
     * @param bool    $isAdmin
     * @param string  $language
     * @param integer $perPage
     * 
     * @return User
     */
    public function createUser($name, $emailAddress, $password, $isAdmin = false, $language = null, $perPage = null)
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($emailAddress);
        $user->setHash(password_hash($password, PASSWORD_DEFAULT));
        $user->setIsAdmin(($isAdmin ? 1 : 0));
        $user->setLanguage($language);
        $user->setPerPage($perPage);

        return $this->store->save($user);
    }

    /**
     * Create a new user within PHPCI (with provider).
     * @param $name
     * @param $emailAddress
     * @param $id
     * @param $password
     * @param $providerKey
     * @param $providerData
     * @param bool $isAdmin
     * @return \PHPCI\Model\User
     */

    public function createUserWithProvider($name, $emailAddress, $id, $password, $providerKey, $providerData, $isAdmin = false)
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($emailAddress);
        $user->setHash("");
        $user->setProviderKey($providerKey);
        $user->setProviderData($providerData);
        $user->setIsAdmin(($isAdmin ? 1 : 0));

        return $this->store->save($user);
    }

    /**
     * Update a user.
     * 
     * @param User    $user
     * @param string  $name
     * @param string  $emailAddress
     * @param string  $password
     * @param bool    $isAdmin
     * @param string  $language
     * @param integer $perPage
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
            $user->setIsAdmin(($isAdmin ? 1 : 0));
        }

        $user->setLanguage($language);
        $user->setPerPage($perPage);

        return $this->store->save($user);
    }

    /**
     * Delete a user.
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user)
    {
        return $this->store->delete($user);
    }
}
