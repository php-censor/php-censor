<?php

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithSingleRequiredArg extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'example_plugin_with_single_required_arg';
    }

    public $RequiredArgument;

    function __construct($requiredArgument)
    {
        $this->RequiredArgument = $requiredArgument;
    }

    public function execute()
    {

    }
}
