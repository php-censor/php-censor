<?php

namespace PHPCensor\Helper;

/**
 * User Helper - Provides access to logged in user information in views.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class User
{
    /**
     * Proxies method calls through to the current active user model.
     * @param $method
     * @param array $params
     * @return mixed|null
     */
    public function __call($method, $params = [])
    {
        if (empty($_SESSION['php-censor-user'])) {
            return null;
        }

        $user = $_SESSION['php-censor-user'];

        if (!is_object($user)) {
            return null;
        }

        return call_user_func_array([$user, $method], $params);
    }
}
