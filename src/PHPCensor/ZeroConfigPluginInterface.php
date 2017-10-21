<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
interface ZeroConfigPluginInterface
{
    public static function canExecute($stage, Builder $builder, Build $build);
}
