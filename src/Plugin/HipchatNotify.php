<?php

namespace PHPCensor\Plugin;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Plugin;

/**
 * Hipchat Plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author James Inman <james@jamesinman.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class HipchatNotify extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'hipchat_notify';
    }

    /**
     * Run the HipChat plugin.
     * @return bool
     * @throws RuntimeException
     */
    public function execute()
    {
        throw new RuntimeException('HipChat has been shut down and is no longer available.');
    }
}
