<?php

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithSingleOptionalArg extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'example_plugin_with_single_optional_arg';
    }
    
    function __construct($optional = null)
    {

    }

    public function execute()
    {

    }
}
