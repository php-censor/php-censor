<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Environment variable plugin
 * 
 * @author       Steve Kamerman <stevekamerman@gmail.com>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Env extends Plugin
{
    protected $env_vars;

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
                $this->builder->logFailure(Lang::get('unable_to_set_env'));
            }
        }
        return $success;
    }
}
