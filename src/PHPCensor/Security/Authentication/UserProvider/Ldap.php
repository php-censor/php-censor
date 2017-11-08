<?php

namespace PHPCensor\Security\Authentication\UserProvider;

use b8\Store\Factory;
use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;
use PHPCensor\Service\UserService;
use PHPCensor\Store\UserStore;

/**
 * Ldap user provider.
 *
 * @author Dmitrii Zolotov (@itherz)
 */
class Ldap extends AbstractProvider implements LoginPasswordProviderInterface
{
    /**
     * @param User   $user
     * @param string $password
     *
     * @return bool
     */
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

    /**
     * @param string $identifier
     *
     * @return User
     */
    public function provisionUser($identifier)
    {
        /** @var UserStore $user */
        $user        = Factory::getStore('User');
        $userService = new UserService($user);

        $parts    = explode("@", $identifier);
        $username = $parts[0];

        return $userService->createUser($username, $identifier, $this->key, json_encode($this->config), '', false);
    }
}
