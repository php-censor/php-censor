<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithSingleOptionalArg extends Plugin
{
    public static function pluginName(): string
    {
        return 'example_plugin_with_single_optional_arg';
    }

    public function __construct($optional = null)
    {
    }

    public function execute(): void
    {
    }
}
