<?php

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithSingleTypedRequiredArg extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'example_plugin_with_single_typed_required_arg';
    }

    public $RequiredArgument;

    public function __construct(\stdClass $requiredArgument)
    {
        $this->RequiredArgument = $requiredArgument;
    }

    public function execute()
    {
    }
}
