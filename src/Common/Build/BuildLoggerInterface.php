<?php

declare(strict_types=1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildLoggerInterface
{
    public function logWarning(string $message): void;

    public function logSuccess(string $message): void;

    public function logDebug(string $message): void;

    public function logNormal(string $message): void;

    public function logFailure(
        string $message,
        ?\Throwable $exception = null
    ): void;
}
