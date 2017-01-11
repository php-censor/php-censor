<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithNoConstructorArgs extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'example_plugin_with_no_constructor_args';
    }
    
    public function execute()
    {
    }
}
