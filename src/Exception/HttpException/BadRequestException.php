<?php

namespace PHPCensor\Exception\HttpException;

use PHPCensor\Exception\HttpException;

class BadRequestException extends HttpException
{
    /**
     * @var integer
     */
    protected $errorCode = 400;

    /**
     * @var string
     */
    protected $statusMessage = 'Bad Request';
}
