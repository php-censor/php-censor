<?php

declare(strict_types=1);

namespace PHPCensor\Plugin\Util;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Factory
{
    private Builder $builder;

    private Build $build;

    public function __construct(
        Builder $builder,
        Build $build
    ) {
        $this->builder = $builder;
        $this->build = $build;
    }

    public function buildPlugin(string $className, array $options = []): Plugin
    {
        return new $className($this->builder, $this->build, $options);
    }

    public function getBuild(): Build
    {
        return $this->build;
    }
}
