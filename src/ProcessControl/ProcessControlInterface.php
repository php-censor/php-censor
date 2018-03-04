<?php

namespace PHPCensor\ProcessControl;

/**
 * A stateless service to check and kill system processes.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface ProcessControlInterface
{
    /**
     * Checks if a process exists.
     *
     * @param int $pid The process identifier.
     *
     * @return boolean true is the process is running, else false.
     */
    public function isRunning($pid);

    /**
     * Terminate a running process.
     *
     * @param int $pid The process identifier.
     * @param bool $forcefully Whether to gently (false) or forcefully (true) terminate the process.
     *
     * @return boolean
     */
    public function kill($pid, $forcefully = false);
}
