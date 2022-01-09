<?php

declare(strict_types = 1);

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Model\User;
use PHPCensor\Security\Authentication\LoginPasswordProviderInterface;
use PHPCensor\Service\UserService;
use PHPCensor\Store\UserStore;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitrii Zolotov (@itherz)
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Ldap extends AbstractProvider implements LoginPasswordProviderInterface
{
    public function verifyPassword(User $user, string $password): bool
    {
        if (isset($this->config['data'])) {
            $ldapData   = $this->config['data'];
            $ldapPort   = !empty($ldapData['port']) ? $ldapData['port'] : null;
            $ldapHost   = !empty($ldapData['host']) ? $ldapData['host'] : 'localhost';
            $ldapBaseDn = !empty($ldapData['base_dn']) ? $ldapData['base_dn'] : 'dc=nodomain';
            $ldapMail   = !empty($ldapData['mail_attribute']) ? $ldapData['mail_attribute'] : 'mail';

            if ($ldapPort) {
                $ldap = @\ldap_connect($ldapHost, $ldapPort);
            } else {
                $ldap = @\ldap_connect($ldapHost);
            }

            if (false === $ldap) {
                return false;
            }

            \ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

            $ls = @\ldap_search($ldap, $ldapBaseDn, $ldapMail . '=' . $user->getEmail());
            if (false === $ls) {
                return false;
            }

            $le = @\ldap_get_entries($ldap, $ls);
            if (!$le['count']) {
                return false;
            }

            $dn = $le[0]['dn'];

            return @\ldap_bind($ldap, $dn, $password);
        }

        return false;
    }

    public function checkRequirements(): void
    {
        // Always fine
    }

    /**
     * @param string|null $identifier
     *
     * @return User|null
     *
     * @throws \PHPCensor\Common\Exception\RuntimeException
     */
    public function provisionUser(?string $identifier): ?User
    {
        /** @var UserStore $user */
        $user        = $this->storeRegistry->get('User');
        $userService = new UserService($this->storeRegistry, $user);

        $parts    = \explode("@", $identifier);
        $username = $parts[0];

        return $userService->createUser($username, $identifier, $this->key, $this->config, '', false);
    }
}
