<?php

namespace PHPCensor\Helper;

/**
 * Unix/Linux specific extension of the CommandExecutor class.
 */
class UnixCommandExecutor extends BaseCommandExecutor
{
    /**
     * Uses 'which' to find a system binary by name.
     * @param string $binary
     * @return null|string
     */
    protected function findGlobalBinary($binary)
    {
        return trim(shell_exec('which ' . $binary));
    }
}
