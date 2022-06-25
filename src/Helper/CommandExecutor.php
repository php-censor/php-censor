<?php

namespace PHPCensor\Helper;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Logging\BuildLogger;
use Symfony\Component\Process\Process;
use PHPCensor\Common\CommandExecutorInterface;

/**
 * Handles running system commands with variables.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
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
     * @var bool
     */
    protected $logExecOutput = true;

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
     * Environment variables that should not be inherited
     *
     * @var array
     */
    private static $blacklistEnvVars = [
        'PHP_SELF',
        'SCRIPT_NAME',
        'SCRIPT_FILENAME',
        'PATH_TRANSLATED',
        'DOCUMENT_ROOT',
        'SHELL_VERBOSITY',
    ];

    /**
     * @param string $rootDir
     * @param bool   $verbose
     */
    public function __construct(BuildLogger $logger, $rootDir, $verbose = false)
    {
        $this->logger     = $logger;
        $this->verbose    = $verbose;
        $this->lastOutput = [];
        $this->rootDir    = $rootDir;
    }



    public function enableCommandOutput(): void
    {
        $this->logExecOutput = true;
    }

    public function disableCommandOutput(): void
    {
        $this->logExecOutput = false;
    }

    public function isEnabledCommandOutput(): bool
    {
        return $this->logExecOutput;
    }

    /**
     * {@inheritDoc}
     */
    public function executeCommand(...$params): bool
    {
        $this->lastOutput = [];

        $this->logger->logDebug('Args: ' . \json_encode($params));

        $command = \call_user_func_array('sprintf', $params);

        $this->logger->logNormal('Shell command: ' . $command);

        $withNoExit = '';
        foreach (self::$noExitCommands as $nec) {
            if (\preg_match("/\b{$nec}\b/", $command)) {
                $withNoExit = $nec;

                break;
            }
        }

        $cwd = RUNTIME_DIR . 'builds';
        if ($this->buildPath && \file_exists($this->buildPath)) {
            $cwd = $this->buildPath;
        }

        $process = Process::fromShellCommandline($command, $cwd);
        $process->setTimeout(86400);

        $env = $this->getDefaultEnv();

        if (!empty($withNoExit)) {
            $process->start(null, $env);

            $this->logger->logDebug("Assuming command '{$withNoExit}' does not exit properly");
            do {
                \sleep(15);
                $response = [];
                \exec("ps auxww | grep '{$withNoExit}' | grep -v grep", $response);
                $response = \array_filter(
                    $response,
                    function ($a) {
                        return \strpos($a, $this->buildPath) !== false;
                    }
                );
            } while (!empty($response));
            $process->stop();
            $status = 0;
        } else {
            $process->setIdleTimeout(600);
            $process->start(null, $env);
            $status = $process->wait();
        }

        $lastOutput = $this->replaceIllegalCharacters($process->getOutput());
        $lastError  = $this->replaceIllegalCharacters($process->getErrorOutput());

        $this->lastOutput = \array_filter(\explode(PHP_EOL, $lastOutput));
        $this->lastError  = $lastError;

        $shouldOutput = ($this->logExecOutput && ($this->verbose || 0 !== $status));

        if ($shouldOutput && !empty($this->lastOutput)) {
            $this->logger->logNormal($this->lastOutput);
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
        \mb_substitute_character(0xFFFD); // is '�'
        $legalUtf8String = \mb_convert_encoding($utf8String, 'utf8', 'utf8');
        $regexp          = '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[^\x{0}-\x{ffff}]/u'; // more than 3 byte UTF-8 sequences (unsupported in mysql)

        return \preg_replace($regexp, '�', $legalUtf8String);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastCommandOutput(): string
    {
        return \implode(PHP_EOL, $this->lastOutput);
    }

    /**
     * {@inheritDoc}
     */
    public function findBinary(
        array $binaryNames,
        string $binaryPath = ''
    ): string {
        if ($binaryPath) {
            if (\file_exists($binaryPath)) {
                $this->logger->logDebug(\sprintf('Found in %s (binary_path)', $binaryPath));

                return $binaryPath;
            }

            if (\is_dir($binaryPath)) {
                foreach ($binaryNames as $binaryName) {
                    $this->logger->logDebug(\sprintf('Looking for binary: %s in: %s', $binaryName, $binaryPath));
                    if (\file_exists($binaryPath . '/' . $binaryName)) {
                        $this->logger->logDebug(\sprintf('Found in %s (binary_path): %s', $binaryPath, $binaryName));

                        return $binaryPath . '/' . $binaryName;
                    }
                }
            }
        }

        $composerBin = $this->getComposerBinDir($this->buildPath);
        foreach ($binaryNames as $binaryName) {
            $this->logger->logDebug(\sprintf('Looking for binary: %s in: %s', $binaryName, $composerBin));
            if (\is_dir($composerBin) && \is_file($composerBin . '/' . $binaryName)) {
                $this->logger->logDebug(\sprintf('Found in %s (local): %s', $composerBin, $binaryName));

                return $composerBin . '/' . $binaryName;
            }
        }

        foreach ($binaryNames as $binaryName) {
            $tempBinary = \trim(\shell_exec('which ' . $binaryName));
            if (\file_exists($tempBinary)) {
                $this->logger->logDebug(\sprintf('Found in %s (system): %s', '', $binaryName));

                return $tempBinary;
            }
        }

        throw new RuntimeException(\sprintf('Could not find %s', \implode('|', $binaryNames)));
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
        if (\is_dir($path)) {
            $composer = $path . '/composer.json';
            if (\is_file($composer)) {
                $json = \json_decode(\file_get_contents($composer));

                if (isset($json->config->{"bin-dir"})) {
                    return $path . '/' . $json->config->{"bin-dir"};
                } elseif (\is_dir($path . '/vendor/bin')) {
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

    private function getDefaultEnv()
    {
        $env = [];

        foreach ($_SERVER as $k => $v) {
            if (\in_array($k, self::$blacklistEnvVars, true)) {
                continue;
            }
            if (\is_string($v) && false !== $v = \getenv($k)) {
                $env[$k] = $v;
            }
        }

        foreach ($_ENV as $k => $v) {
            if (\in_array($k, self::$blacklistEnvVars, true)) {
                continue;
            }
            if (\is_string($v)) {
                $env[$k] = $v;
            }
        }

        if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 1) {
            foreach (\getenv() as $k => $v) {
                if (\in_array($k, self::$blacklistEnvVars, true)) {
                    continue;
                }
                if (\is_string($v)) {
                    $env[$k] = $v;
                }
            }
        } else {
            $output = [];
            \exec('env', $output);
            foreach ($output as $o) {
                $keyVal = \explode('=', $o, 2);
                if (\count($keyVal) < 2 || empty($keyVal[1])) {
                    continue;
                }
                if (\in_array($keyVal[0], self::$blacklistEnvVars, true)) {
                    continue;
                }
                $env[$keyVal[0]] = $keyVal[1];
            }
        }

        return $env;
    }
}
