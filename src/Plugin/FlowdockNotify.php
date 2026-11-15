<?php

namespace PHPCensor\Plugin;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Plugin;

/**
 * Flowdock Plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Petr Cervenka <petr@nanosolutions.io>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class FlowdockNotify extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'flowdock_notify';
    }

    /**
     * Run the Flowdock plugin.
     * @return bool
     * @throws RuntimeException
     */
    public function execute()
    {
        throw new RuntimeException('Flowdock has been shut down and is no longer available.');
    }
}
