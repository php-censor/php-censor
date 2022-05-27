<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

class ExamplePluginFull extends Plugin
{
    public $options;

    public static function pluginName(): string
    {
        return 'example_plugin_full';
    }

    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        $this->options = $options;
    }

    public function execute(): void
    {
    }
}
