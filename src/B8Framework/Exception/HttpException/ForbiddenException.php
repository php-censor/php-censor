<?php

namespace b8\Exception\HttpException;

use b8\Exception\HttpException;

class ForbiddenException extends HttpException
{
    /**
     * @var integer
     */
    protected $errorCode = 403;

    /**
     * @var string
     */
    protected $statusMessage = 'Forbidden';
}
