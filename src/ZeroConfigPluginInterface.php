<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
interface ZeroConfigPluginInterface
{
    /**
     * @param string $stage
     *
     * @return bool
     */
    public static function canExecuteOnStage($stage, Build $build);
}
