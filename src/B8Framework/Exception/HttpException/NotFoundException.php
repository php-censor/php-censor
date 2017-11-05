<?php

namespace b8\Exception\HttpException;

use b8\Exception\HttpException;

class NotFoundException extends HttpException
{
    /**
     * @var integer
     */
    protected $errorCode = 404;

    /**
     * @var string
     */
    protected $statusMessage = 'Not Found';
}
