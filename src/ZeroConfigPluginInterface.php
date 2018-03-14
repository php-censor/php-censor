<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
interface ZeroConfigPluginInterface
{
    /**
     * @param string  $stage
     * @param Builder $builder
     * @param Build   $build
     *
     * @return mixed
     */
    public static function canExecute($stage, Builder $builder, Build $build);
}
