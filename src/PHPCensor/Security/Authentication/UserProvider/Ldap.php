<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright Copyright 2014, Block 8 Limited.
 * @license   https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link      https://www.phptesting.org/
 */

namespace PHPCensor\Security\Authentication\UserProvider;

use b8\Store\Factory;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;
use PHPCensor\Service\UserService;

/**
 * Ldap user provider.
 *
 * @author Dmitrii Zolotov (@itherz)
 */
class Ldap extends AbstractProvider implements LoginPasswordProviderInterface
{
    public function verifyPassword(User $user, $password)
    {
        if (isset($this->config['data'])) {
            $ldapData   = $this->config['data'];
            $ldapPort   = !empty($ldapData['port']) ? $ldapData['port'] : null;
            $ldapHost   = !empty($ldapData['host']) ? $ldapData['host'] : 'localhost';
            $ldapBaseDn = !empty($ldapData['base_dn']) ? $ldapData['base_dn'] : 'dc=nodomain';
            $ldapMail   = !empty($ldapData['mail_attribute']) ? $ldapData['mail_attribute'] : 'mail';

            if ($ldapPort) {
                $ldap = @ldap_connect($ldapHost, $ldapPort);
            } else {
                $ldap = @ldap_connect($ldapHost);
            }

            if (false === $ldap) {
                return false;
            }

            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

            $ls = @ldap_search($ldap, $ldapBaseDn, $ldapMail . '=' . $user->getEmail());
            if (false === $ls) {
                return false;
            }

            $le = @ldap_get_entries($ldap, $ls);
            if (!$le['count']) {
                return false;
            }

            $dn = $le[0]['dn'];

            return @ldap_bind($ldap, $dn, $password);
        }

        return false;
    }

    public function checkRequirements()
    {
        // Always fine
    }

    public function provisionUser($identifier)
    {
        $userService = new UserService(Factory::getStore('User'));

        $parts    = explode("@", $identifier);
        $username = $parts[0];

        return $userService->createUserWithProvider($username, $identifier, $this->key, null);
    }
}
