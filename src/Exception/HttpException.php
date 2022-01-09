<?php

declare(strict_types=1);

namespace PHPCensor\Exception;

use PHPCensor\Common\Exception\Exception;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class HttpException extends Exception
{
    protected int $errorCode = 500;

    protected string $statusMessage = 'Internal Server Error';

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function getHttpHeader(): string
    {
        return 'HTTP/1.1 ' . $this->errorCode . ' ' . $this->statusMessage;
    }
}
