<?php

namespace PHPCensor\Helper;

use b8\Config;

/**
 * Login Is Disabled Helper - Checks if login is disabled in the view
 * 
 * @author Stephen Ball <phpci@stephen.rebelinblue.com>
 */
class LoginIsDisabled
{
    /**
     * Checks if
     *
     * @param $method
     * @param array $params
     *
     * @return mixed|null
     */
    public function __call($method, $params = [])
    {
        unset($method, $params);
        
        $config      = Config::getInstance();
        $disableAuth = (boolean)$config->get('php-censor.security.disable_auth', false);

        return $disableAuth;
    }
}
