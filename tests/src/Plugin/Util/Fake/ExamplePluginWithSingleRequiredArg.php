<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Plugin;

class ExamplePluginWithSingleRequiredArg extends Plugin
{
    public static function pluginName(): string
    {
        return 'example_plugin_with_single_required_arg';
    }

    public $RequiredArgument;

    public function __construct($requiredArgument)
    {
        $this->RequiredArgument = $requiredArgument;
    }

    public function execute(): void
    {
    }
}
