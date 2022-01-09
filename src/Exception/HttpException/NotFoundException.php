<?php

declare(strict_types=1);

namespace PHPCensor\Exception\HttpException;

use PHPCensor\Exception\HttpException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class NotFoundException extends HttpException
{
    protected int $errorCode = 404;

    protected string $statusMessage = 'Not Found';
}
