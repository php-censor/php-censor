<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
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
