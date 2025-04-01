<?php

declare(strict_types=1);

namespace PHPCensor\Logging;

use PHPCensor\Model\Build;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildLogger
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Build $build
    ) {
    }

    public function log($message, string $level = LogLevel::INFO, array $context = []): void
    {
        if (!\is_array($message)) {
            $message = [$message];
        }

        $context['build'] = $this->build->getId();

        foreach ($message as $item) {
            $this->logger->log($level, $item, ($item ? $context : []));
        }
    }

    public function logWarning(string $message): void
    {
        $this->log("\033[0;31m" . $message . "\033[0m");
    }

    public function logSuccess(string $message): void
    {
        $this->log("\033[0;32m" . $message . "\033[0m");
    }

    public function logFailure(string $message, ?\Throwable $exception = null): void
    {
        $level   = LogLevel::INFO;
        $context = [];

        if ($exception) {
            $level = LogLevel::ERROR;

            $context['exception'] = $exception;
            $context['trace']     = $exception->getTrace();
        }

        $this->log("\033[0;31m" . $message . "\033[0m", $level, $context);
    }

    public function logDebug(string $message): void
    {
        if ($this->build->isDebug()) {
            $this->log("\033[0;36m" . $message . "\033[0m", LogLevel::DEBUG);
        }
    }
}
