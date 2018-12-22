<?php

namespace PHPCensor\Helper;

use Exception;
use PHPCensor\Logging\BuildLogger;

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
    protected $verbose;

    /**
     * @var array
     */
    protected $lastOutput;

    /**
     * @var string
     */
    protected $lastError;

    /**
     * @var boolean
     */
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
     * @param bool        $verbose
     */
    public function __construct(BuildLogger $logger, $rootDir, $verbose = false)
    {
        $this->logger     = $logger;
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

        $status         = 0;
        $descriptorSpec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"], // stderr
        ];

        $pipes   = [];
        $process = proc_open($command, $descriptorSpec, $pipes, $this->buildPath, null);

        $lastOutput = '';
        $lastError  = '';
        if (is_resource($process)) {
            fclose($pipes[0]);

            list($lastOutput, $lastError) = $this->readAlternating([$pipes[1], $pipes[2]]);

            $status = (int)proc_close($process);

            $lastOutput = $this->replaceIllegalCharacters($lastOutput);
            $lastError  = $this->replaceIllegalCharacters($lastError);
        }

        $this->lastOutput = array_filter(explode(PHP_EOL, $lastOutput));
        $this->lastError  = $lastError;

        $shouldOutput = ($this->logExecOutput && ($this->verbose || 0 !== $status));

        if ($shouldOutput && !empty($this->lastOutput)) {
            $this->logger->log($this->lastOutput);
        }

        if (!empty($this->lastError)) {
            $this->logger->logFailure($this->lastError);
        }

        $rtn = false;
        if (0 === $status) {
            $rtn = true;
        }

        $this->logger->logDebug('Execution status: ' . $status);

        return $rtn;
    }

    /**
     * Reads from array of streams as data becomes available.
     *
     * @param array $descriptors
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
        $retries = 6;
        $timeout = 15;
        do {
            $resources = 0;
            $read      = [];
            for ($i = 0; $i < $retries; ++$i) {
                $read      = $descriptors;
                $write     = null;
                $except    = null;
                $resources = stream_select($read, $write, $except, $timeout);
                if (intval($resources) > 0) {
                    break;
                }
            }
            foreach ($read as $descriptor) {
                $key = array_search($descriptor, $descriptors);
                if (feof($descriptor)) {
                    fclose($descriptor);
                    unset($descriptors[$key]);
                } else {
                    $buffer = fgets($descriptor);
                    if (false === $buffer) {
                        fclose($descriptor);
                        unset($descriptors[$key]);
                        continue;
                    }
                    $outputs[$key] .= $buffer;
                }
            }
        } while (count($descriptors) > 0 && intval($resources) > 0);
        return $outputs;
    }

    /**
     * @param string $utf8String
     *
     * @return string
     */
    public function replaceIllegalCharacters($utf8String)
    {
        mb_substitute_character(0xFFFD); // is '�'
        $legalUtf8String = mb_convert_encoding($utf8String, 'utf8', 'utf8');
        $regexp          = '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[^\x{0}-\x{ffff}]/u'; // more than 3 byte UTF-8 sequences (unsupported in mysql)

        return preg_replace($regexp, '�', $legalUtf8String);
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
     * @param string $binaryPath
     * @param string $binary
     *
     * @return string|false
     */
    protected function findBinaryByPath($binaryPath, $binary)
    {
        if (is_dir($binaryPath) && is_file($binaryPath . '/' . $binary)) {
            $this->logger->logDebug(sprintf('Found in %s (binary_path): %s', $binaryPath, $binary));

            return $binaryPath . '/' . $binary;
        }

        return false;
    }

    /**
     * @param string $composerBin
     * @param string $binary
     *
     * @return string|false
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
     * @return string|false
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
     * @return string|false
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
     * @param array|string $binary
     * @param string       $priorityPath
     * @param string       $binaryPath
     * @param string       $binaryName
     * @return string
     *
     * @throws \Exception when no binary has been found.
     */
    public function findBinary($binary, $priorityPath = 'local', $binaryPath = '', $binaryName = '')
    {
        $composerBin = $this->getComposerBinDir(realpath($this->buildPath));

        if (is_string($binary)) {
            $binary = [$binary];
        }

        //overwrite binary name
        if ($binaryName) {
            if (is_string($binaryName)) {
                $binaryName = [$binaryName];
            }

            array_unshift($binary, ...$binaryName);
        }

        foreach ($binary as $bin) {
            $this->logger->logDebug(sprintf('Looking for binary: %s, priority = %s', $bin, $priorityPath));

            if ('binary_path' === $priorityPath) {
                if ($binaryPath = $this->findBinaryByPath($binaryPath, $bin)) {
                    return $binaryPath;
                }

                if ($binaryLocal = $this->findBinaryLocal($composerBin, $bin)) {
                    return $binaryLocal;
                }

                if ($binaryGlobal = $this->findBinaryGlobal($bin)) {
                    return $binaryGlobal;
                }

                if ($binarySystem = $this->findBinarySystem($bin)) {
                    return $binarySystem;
                }
            } elseif ('system' === $priorityPath) {
                if ($binarySystem = $this->findBinarySystem($bin)) {
                    return $binarySystem;
                }

                if ($binaryLocal = $this->findBinaryLocal($composerBin, $bin)) {
                    return $binaryLocal;
                }

                if ($binaryGlobal = $this->findBinaryGlobal($bin)) {
                    return $binaryGlobal;
                }

                if ($binaryPath = $this->findBinaryByPath($binaryPath, $bin)) {
                    return $binaryPath;
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

                if ($binaryPath = $this->findBinaryByPath($binaryPath, $bin)) {
                    return $binaryPath;
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

                if ($binaryPath = $this->findBinaryByPath($binaryPath, $bin)) {
                    return $binaryPath;
                }
            }
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
            $composer = $path . '/composer.json';
            if (is_file($composer)) {
                $json = json_decode(file_get_contents($composer));

                if (isset($json->config->{"bin-dir"})) {
                    return $path . '/' . $json->config->{"bin-dir"};
                } elseif (is_dir($path . '/vendor/bin')) {
                    return $path . '/vendor/bin';
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
