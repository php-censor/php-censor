<?php

declare(strict_types = 1);

namespace PHPCensor\Logging;

use ErrorException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Handler
{
    protected array $levels = [
        E_WARNING           => 'Warning',
        E_NOTICE            => 'Notice',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
    ];

    protected ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public static function register(?LoggerInterface $logger = null): void
    {
        $handler = new static($logger);

        \set_error_handler([$handler, 'handleError']);
        \register_shutdown_function([$handler, 'handleFatalError']);

        \set_exception_handler([$handler, 'handleException']);
    }

    /**
     * @param int     $level
     * @param string  $message
     * @param string  $file
     * @param int     $line
     *
     * @throws ErrorException
     */
    public function handleError(int $level, string $message, string $file, int $line): void
    {
        if (\error_reporting() & $level) {
            $exceptionLevel = $this->levels[$level] ?? $level;

            throw new ErrorException(
                \sprintf('%s: %s in %s line %d', $exceptionLevel, $message, $file, $line),
                0,
                $level,
                $file,
                $line
            );
        }
    }

    public function handleFatalError(): void
    {
        $fatalError = \error_get_last();

        try {
            if (\error_get_last() !== null) {
                $error = new ErrorException(
                    \sprintf(
                        '%s: %s in %s line %d',
                        $fatalError['type'],
                        $fatalError['message'],
                        $fatalError['file'],
                        $fatalError['line']
                    ),
                    0,
                    $fatalError['type'],
                    $fatalError['file'],
                    $fatalError['line']
                );
                $this->log($error);
            }
        } catch (Exception $e) {
            $error = new ErrorException(
                sprintf(
                    '%s: %s in %s line %d',
                    $fatalError['type'],
                    $fatalError['message'],
                    $fatalError['file'],
                    $fatalError['line']
                ),
                0,
                $fatalError['type'],
                $fatalError['file'],
                $fatalError['line']
            );
            $this->log($error);
        }
    }

    public function handleException(\Throwable $exception): void
    {
        $this->log($exception);
    }

    protected function log(\Throwable $exception): void
    {
        if (null !== $this->logger) {
            $message = \sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                \get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );

            $this->logger->error($message, ['exception' => $exception]);
        }
    }
}
