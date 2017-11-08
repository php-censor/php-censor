<?php

namespace PHPCensor\ProcessControl;

/**
 * Control processes using the "ps" and "kill" commands.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class UnixProcessControl implements ProcessControlInterface
{
    /**
     * Check process using the "ps" command.
     *
     * @param int $pid
     *
     * @return boolean
     */
    public function isRunning($pid)
    {
        $output = $exitCode = null;
        exec(sprintf("ps %d", $pid), $output, $exitCode);
        return $exitCode === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function kill($pid, $forcefully = false)
    {
        $output = [];
        $result = 1;
        
        exec(sprintf("kill -%d %d", $forcefully ? 9 : 15, $pid), $output, $result);
        
        return !$result;
    }

    /**
     * Check whether the commands "ps" and "kill" are available.
     *
     * @return bool
     *
     * @internal
     */
    public static function isAvailable()
    {
        return DIRECTORY_SEPARATOR === '/' && exec("which ps") && exec("which kill");
    }
}
