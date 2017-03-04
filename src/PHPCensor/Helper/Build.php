<?php

namespace PHPCensor\Helper;

/**
 * User Helper - Provides access to logged in user information in views.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Build
{
    /**
     * Returns a more human-friendly version of a plugin name.
     * @param $name
     * @return mixed
     */
    public function formatPluginName($name)
    {
        return str_replace('Php', 'PHP', ucwords(str_replace('_', ' ', $name)));
    }
}
