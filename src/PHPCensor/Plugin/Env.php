<?php

namespace PHPCensor\Plugin;

use PHPCensor\Plugin;

/**
 * Environment variable plugin
 * 
 * @author Steve Kamerman <stevekamerman@gmail.com>
 */
class Env extends Plugin
{
    protected $env_vars;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'env';
    }
    
    /**
    * Adds the specified environment variables to the builder environment
    */
    public function execute()
    {
        $success = true;
        foreach ($this->options as $key => $value) {
            if (is_numeric($key)) {
                // This allows the developer to specify env vars like " - FOO=bar" or " - FOO: bar"
                $env_var = is_array($value)? key($value).'='.current($value): $value;
            } else {
                // This allows the standard syntax: "FOO: bar"
                $env_var = "$key=$value";
            }

            if (!putenv($this->builder->interpolate($env_var))) {
                $success = false;
                $this->builder->logFailure('Unable to set environment variable');
            }
        }
        return $success;
    }
}
