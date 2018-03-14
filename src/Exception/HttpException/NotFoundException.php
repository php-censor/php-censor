<?php

namespace PHPCensor\Exception\HttpException;

use PHPCensor\Exception\HttpException;

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
