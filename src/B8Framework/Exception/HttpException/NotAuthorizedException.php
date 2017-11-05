<?php

namespace b8\Exception\HttpException;

use b8\Exception\HttpException;

class NotAuthorizedException extends HttpException
{
    /**
     * @var integer
     */
    protected $errorCode = 401;

    /**
     * @var string
     */
    protected $statusMessage = 'Not Authorized';
}
