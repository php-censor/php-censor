<?php

namespace PHPCensor\Exception;

class HttpException extends \Exception
{
    /**
     * @var integer
     */
    protected $errorCode = 500;

    /**
     * @var string
     */
    protected $statusMessage = 'Internal Server Error';

    /**
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return string
     */
    public function getHttpHeader()
    {
        return 'HTTP/1.1 ' . $this->errorCode . ' ' . $this->statusMessage;
    }
}
