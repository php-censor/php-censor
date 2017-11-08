<?php

namespace PHPCensor\Helper;

use Exception;
use PHPCensor\Logging\BuildLogger;
use Psr\Log\LogLevel;

/**
 * Handles running system commands with variables.
 */
class CommandExecutor implements CommandExecutorInterface
{
    /**
     * @var BuildLogger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $quiet;

    /**
     * @var bool
     */
    protected $verbose;

    /**
     * @var array
     */
    protected $lastOutput;

    /**
     * @var string
     */
    protected $lastError;

    public $logExecOutput = true;

    /**
     * The path which findBinary will look in.
     *
     * @var string
     */
    protected $rootDir;

    /**
     * Current build path
     *
     * @var string
     */
    protected $buildPath;

    /**
     * @param BuildLogger $logger
     * @param string      $rootDir
     * @param bool        $quiet
     * @param bool        $verbose
     */
    public function __construct(BuildLogger $logger, $rootDir, &$quiet = false, &$verbose = false)
    {
        $this->logger     = $logger;
        $this->quiet      = $quiet;
        $this->verbose    = $verbose;
        $this->lastOutput = [];
        $this->rootDir    = $rootDir;
    }

    /**
     * Executes shell commands.
     *
     * @param array $args
     *
     * @return bool Indicates success
     */
    public function executeCommand($args = [])
    {
        $this->lastOutput = [];

        $this->logger->logDebug('Args: ' . json_encode($args));

        $command = call_user_func_array('sprintf', $args);

        $this->logger->logDebug('Command: ' . $command);

        if ($this->quiet) {
            $this->logger->log('Executing: ' . $command);
        }

        $status = 0;
        $descriptorSpec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"], // stderr
        ];

        $pipes   = [];
        $process = proc_open($command, $descriptorSpec, $pipes, $this->buildPath, null);

        $this->lastOutput = '';
        $this->lastError  = '';
        
        if (is_resource($process)) {
            fclose($pipes[0]);

            list($this->lastOutput, $this->lastError) = $this->readAlternating([$pipes[1], $pipes[2]]);

            $status = proc_close($process);
        }

        mb_substitute_character(65533);

        $this->lastOutput = mb_convert_encoding($this->lastOutput, 'utf8', 'utf8');
        $this->lastOutput = array_filter(explode(PHP_EOL, $this->lastOutput));

        $shouldOutput = ($this->logExecOutput && ($this->verbose || $status != 0));

        if ($shouldOutput && !empty($this->lastOutput)) {
            $this->logger->log($this->lastOutput);
        }

        if (!empty($this->lastError)) {
            $this->logger->log("\033[0;31m" . $this->lastError . "\033[0m", LogLevel::ERROR);
        }

        $rtn = false;
        if ($status == 0) {
            $rtn = true;
        }

        $this->logger->logDebug('Execution status: ' . $status);

        return $rtn;
    }

    /**
     * Reads from array of streams as data becomes available.
     *
     * @param array     $descriptors
     *
     * @return string[] data read from each descriptor
     */
    private function readAlternating(array $descriptors)
    {
        $outputs = [];
        foreach ($descriptors as $key => $descriptor) {
            stream_set_blocking($descriptor, false);
            $outputs[$key] = '';
        }
        do {
            $read = $descriptors;
            $write = null;
            $except = null;
            stream_select($read, $write, $except, null);
            foreach ($read as $descriptor) {
                $key = array_search($descriptor, $descriptors);
                if (feof($descriptor)) {
                    fclose($descriptor);
                    unset($descriptors[$key]);
                } else {
                    $outputs[$key] .= fgets($descriptor);
                }
            }
        } while (count($descriptors) > 0);
        return $outputs;
    }

    /**
     * Returns the output from the last command run.
     *
     * @return string
     */
    public function getLastOutput()
    {
        return implode(PHP_EOL, $this->lastOutput);
    }

    /**
     * Returns the stderr output from the last command run.
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param string $composerBin
     * @param string $binary
     *
     * @return false|string
     */
    protected function findBinaryLocal($composerBin, $binary)
    {
        if (is_dir($composerBin) && is_file($composerBin . '/' . $binary)) {
            $this->logger->logDebug(sprintf('Found in %s (local): %s', $composerBin, $binary));

            return $composerBin . '/' . $binary;
        }
        
        return false;
    }

    /**
     * @param string $binary
     *
     * @return false|string
     */
    protected function findBinaryGlobal($binary)
    {
        if (is_file($this->rootDir . 'vendor/bin/' . $binary)) {
            $this->logger->logDebug(sprintf('Found in %s (global): %s', 'vendor/bin', $binary));

            return $this->rootDir . 'vendor/bin/' . $binary;
        }

        return false;
    }

    /**
     * Uses 'which' to find a system binary by name
     *
     * @param string $binary
     *
     * @return false|string
     */
    protected function findBinarySystem($binary)
    {
        $tempBinary = trim(shell_exec('which ' . $binary));
        if (is_file($tempBinary)) {
            $this->logger->logDebug(sprintf('Found in %s (system): %s', '', $binary));

            return $tempBinary;
        }

        return false;
    }

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
    public function findBinary($binary, $quiet = false, $priorityPath = 'local')
    {
        $composerBin = $this->getComposerBinDir(realpath($this->buildPath));

        if (is_string($binary)) {
            $binary = [$binary];
        }

        foreach ($binary as $bin) {
            $this->logger->logDebug(sprintf('Looking for binary: %s', $bin));

            if ('system' === $priorityPath) {
                if ($binarySystem = $this->findBinarySystem($bin)) {
                    return $binarySystem;
                }

                if ($binaryLocal = $this->findBinaryLocal($composerBin, $bin)) {
                    return $binaryLocal;
                }

                if ($binaryGlobal = $this->findBinaryGlobal($bin)) {
                    return $binaryGlobal;
                }
            } elseif ('global' === $priorityPath) {
                if ($binaryGlobal = $this->findBinaryGlobal($bin)) {
                    return $binaryGlobal;
                }

                if ($binaryLocal = $this->findBinaryLocal($composerBin, $bin)) {
                    return $binaryLocal;
                }

                if ($binarySystem = $this->findBinarySystem($bin)) {
                    return $binarySystem;
                }
            } else {
                if ($binaryLocal = $this->findBinaryLocal($composerBin, $bin)) {
                    return $binaryLocal;
                }

                if ($binaryGlobal = $this->findBinaryGlobal($bin)) {
                    return $binaryGlobal;
                }

                if ($binarySystem = $this->findBinarySystem($bin)) {
                    return $binarySystem;
                }
            }
        }

        if ($quiet) {
            return null;
        }

        throw new Exception(sprintf('Could not find %s', implode('/', $binary)));
    }

    /**
     * Try to load the composer.json file in the building project
     * If the bin-dir is configured, return the full path to it
     *
     * @param string $path Current build path
     *
     * @return string|null
     */
    public function getComposerBinDir($path)
    {
        if (is_dir($path)) {
            $composer = $path . DIRECTORY_SEPARATOR . 'composer.json';
            if (is_file($composer)) {
                $json = json_decode(file_get_contents($composer));

                if (isset($json->config->{"bin-dir"})) {
                    return $path . DIRECTORY_SEPARATOR . $json->config->{"bin-dir"};
                } elseif (is_dir($path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin')) {
                    return $path  . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin';
                }
            }
        }
        return null;
    }

    /**
     * Set the buildPath property.
     *
     * @param string $path
     */
    public function setBuildPath($path)
    {
        $this->buildPath = $path;
    }
}
