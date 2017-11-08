<?php

namespace PHPCensor\Helper;

interface CommandExecutorInterface
{
    /**
     * Executes shell commands. Accepts multiple arguments the first
     * is the template and everything else is inserted in. c.f. sprintf
     *
     * @param array $args
     *
     * @return boolean Indicates success
     */
    public function executeCommand($args = []);

    /**
     * Returns the output from the last command run.
     *
     * @return string
     */
    public function getLastOutput();

    /**
     * Find a binary required by a plugin.
     *
     * @param string $binary
     * @param bool   $quiet Returns null instead of throwing an exception.
     * @param string $priorityPath
     *
     * @return null|string
     *
     * @throws \Exception when no binary has been found and $quiet is false.
     */
    public function findBinary($binary, $quiet = false, $priorityPath = 'local');

    /**
     * Set the buildPath property.
     *
     * @param string $path
     */
    public function setBuildPath($path);
}
