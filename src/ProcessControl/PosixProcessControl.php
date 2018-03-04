<?php

namespace PHPCensor\ProcessControl;

/**
 * Control process using the POSIX extension.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class PosixProcessControl implements ProcessControlInterface
{
    /**
     * @param integer $pid
     *
     * @return bool
     */
    public function isRunning($pid)
    {
        // Signal "0" is not sent to the process, but posix_kill checks the process anyway;
        return posix_kill($pid, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function kill($pid, $forcefully = false)
    {
        return posix_kill($pid, $forcefully ? 9 : 15);
    }

    /**
     * Check whether this posix_kill is available.
     *
     * @return bool
     *
     * @internal
     */
    public static function isAvailable()
    {
        return function_exists('posix_kill');
    }
}
