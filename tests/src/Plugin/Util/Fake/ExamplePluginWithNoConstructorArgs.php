<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithNoConstructorArgs extends Plugin
{
    public static function pluginName(): string
    {
        return 'example_plugin_with_no_constructor_args';
    }

    public function execute(): void
    {
    }
}
