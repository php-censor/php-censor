<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication\UserProvider;

use b8\Config;
use PHPCI\Model\User;
use PHPCI\Security\Authentication\LoginPasswordProvider;

/**
 * Ldap user provider.
 * @author   Adirelle <adirelle@gmail.com>
 */
class Ldap extends AbstractProvider implements LoginPasswordProvider
{

    public function verifyPassword(User $user, $password)
    {
        $config = Config::getInstance()->get('phpci.security.ldap', []);
	$server = $config["server"];
	$mailAttribute = $config["mailAttribute"];
	$ldap = ldap_connect($server);
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	var_dump($mailAttribute."=".$user->getEmail());
	$ls = ldap_search($ldap, $config["base"], $mailAttribute."=".$user->getEmail());
	$le = ldap_get_entries($ldap, $ls);
	if ($le["count"]==0) return false;
	$dn = $le[0]["dn"];
	return ldap_bind($ldap, $dn, $password);
    }

    public function checkRequirements()
    {
        // Always fine
    }

    public function provisionUser($identifier)
    {
        return null;
    }
}
