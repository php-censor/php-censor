<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\ProcessControl;

/**
 * Control processes using the "tasklist" and "taskkill" commands.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class WindowsProcessControl implements ProcessControlInterface
{
    /**
     * Check if the process is running using the "tasklist" command.
     *
     * @param integer $pid
     * 
     * @return bool
     */
    public function isRunning($pid)
    {
        $lastLine = exec(sprintf('tasklist /fi "PID eq %d" /nh /fo csv 2>nul:', $pid));
        $record = str_getcsv($lastLine);
        return isset($record[1]) && intval($record[1]) === $pid;
    }

    /**
     * {@inheritdoc}
     */
    public function kill($pid, $forcefully = false)
    {
        $output = [];
        $result = 1;

        exec(sprintf("taskkill /t /pid %d %s 2>nul:", $pid, $forcefully ? '/f' : ''));

        return !$result;
    }

    /**
     * Check whether the commands "tasklist" and "taskkill" are available.
     *
     * @return bool
     *
     * @internal
     */
    public static function isAvailable()
    {
        return DIRECTORY_SEPARATOR === '\\' && exec("where tasklist") && exec("where taskkill");
    }
}
