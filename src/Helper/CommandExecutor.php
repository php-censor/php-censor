<?php

namespace PHPCensor\Helper;

use Exception;
use PHPCensor\Logging\BuildLogger;
use Symfony\Component\Process\Process;

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
     * Commands with no proper exit mechanism
     *
     * @var array
     */
    private static $noExitCommands = [
        'codecept',
    ];

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

        $withNoExit = '';
        foreach (self::$noExitCommands as $nec) {
            if (preg_match("/\b{$nec}\b/", $command)) {
                $withNoExit = $nec;
                break;
            }
        }

        $process = new Process($command, $this->buildPath);
        $process->setTimeout(86400);

        if (!empty($withNoExit)) {
            $process->start();

            $this->logger->logDebug("Assuming command '{$withNoExit}' does not exit properly");
            do {
                sleep(15);
                $response = [];
                exec("ps auxww | grep '{$withNoExit}' | grep -v grep", $response);
                $response = array_filter(
                    $response,
                    function ($a) {
                        return strpos($a, $this->buildPath) !== false;
                    }
                );
            } while (!empty($response));
            $process->stop();
            $status = 0;
        } else {
            $process->setIdleTimeout(600);
            $process->start();
            $status = $process->wait();
        }

        $lastOutput = $this->replaceIllegalCharacters($process->getOutput());
        $lastError  = $this->replaceIllegalCharacters($process->getErrorOutput());

        $this->lastOutput = array_filter(explode(PHP_EOL, $lastOutput));
        $this->lastError  = $lastError;

        $shouldOutput = ($this->logExecOutput && ($this->verbose || 0 !== $status));

        if (($shouldOutput || true) && !empty($this->lastOutput)) {
            $this->logger->log($this->lastOutput);
        }

        if (!empty($this->lastError)) {
            $this->logger->logFailure($this->lastError);
        }

        $isSuccess = false;
        if (0 === $status) {
            $isSuccess = true;
        }

        $this->logger->logDebug('Execution status: ' . $status);

        return $isSuccess;
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
     * {@inheritdoc}
     */
    public function findBinary($binary, $priorityPath = 'local', $binaryPath = '', $binaryName = [])
    {
        $composerBin = $this->getComposerBinDir($this->buildPath);

        if (is_string($binary)) {
            $binary = [$binary];
        }

        if ($binaryName) {
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
