<?php

namespace b8\Exception\HttpException;

use b8\Exception\HttpException;

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
