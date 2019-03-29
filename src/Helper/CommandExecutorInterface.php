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
     * @return bool Indicates success
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
     * @param array|string $binary
     * @param string       $priorityPath
     * @param string       $binaryPath
     * @param array        $binaryName
     * @return string
     *
     * @throws \Exception when no binary has been found.
     */
    public function findBinary($binary, $priorityPath = 'local', $binaryPath = '', $binaryName = []);

    /**
     * Set the buildPath property.
     *
     * @param string $path
     */
    public function setBuildPath($path);
}
