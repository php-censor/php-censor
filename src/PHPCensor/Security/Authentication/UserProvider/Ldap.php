<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright Copyright 2014, Block 8 Limited.
 * @license   https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link      https://www.phptesting.org/
 */

namespace PHPCensor\Security\Authentication\UserProvider;

use b8\Config;
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
        $providers = Config::getInstance()->get('php-censor.security.auth_providers', []);
        if ($providers) {
            foreach ($providers as $provider) {
                if (isset($provider['type']) && 'ldap' === $provider['type']) {
                    $ldapData = $provider['data'];

                    $ldap = ldap_connect($ldapData['host'], $ldapData['port']);

                    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

                    $ls = ldap_search($ldap, $ldapData['base_dn'], $ldapData['mail_attribute'] . '=' . $user->getEmail());
                    $le = ldap_get_entries($ldap, $ls);
                    if (!$le['count']) {
                        continue;
                    }

                    $dn = $le[0]['dn'];

                    return @ldap_bind($ldap, $dn, $password);
                }
            }
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
        
        return $userService->createUserWithProvider($username, $identifier, 'ldap', null);
    }
}
