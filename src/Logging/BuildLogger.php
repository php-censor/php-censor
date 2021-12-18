<?php

namespace PHPCensor\Logging;

use Exception;
use PHPCensor\Model\Build;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class BuildLogger
 */
class BuildLogger implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Build
     */
    protected $build;

    public function __construct(LoggerInterface $logger, Build $build)
    {
        $this->logger = $logger;
        $this->build  = $build;
    }

    /**
     * Add an entry to the build log.
     *
     * @param string|string[] $message
     * @param string          $level
     * @param mixed[]         $context
     */
    public function log($message, $level = LogLevel::INFO, $context = [])
    {
        // Skip if no logger has been loaded.
        if (!$this->logger) {
            return;
        }

        if (!is_array($message)) {
            $message = [$message];
        }

        // The build is added to the context so the logger can use
        // details from it if required.
        $context['build'] = $this->build->getId();

        foreach ($message as $item) {
            $this->logger->log($level, $item, ($item ? $context : []));
        }
    }

    /**
     * Add a warning-coloured message to the log.
     *
     * @param string $message
     */
    public function logWarning($message)
    {
        $this->log("\033[0;31m" . $message . "\033[0m");
    }

    /**
     * Add a success-coloured message to the log.
     *
     * @param string $message
     */
    public function logSuccess($message)
    {
        $this->log("\033[0;32m" . $message . "\033[0m");
    }

    /**
     * Add a failure-coloured message to the log.
     *
     * @param string     $message
     * @param Exception $exception The exception that caused the error.
     */
    public function logFailure($message, Exception $exception = null)
    {
        $level   = LogLevel::INFO;
        $context = [];

        // The psr3 log interface stipulates that exceptions should be passed
        // as the exception key in the context array.
        if ($exception) {
            $level = LogLevel::ERROR;

            $context['exception'] = $exception;
            $context['trace']     = $exception->getTrace();
        }

        $this->log("\033[0;31m" . $message . "\033[0m", $level, $context);
    }

    /**
     * Add a debug-coloured message to the log.
     *
     * @param string $message
     */
    public function logDebug($message)
    {
        if ($this->build->isDebug()) {
            $this->log("\033[0;36m" . $message . "\033[0m", LogLevel::DEBUG);
        }
    }

    /**
     * Sets a logger instance on the object
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
