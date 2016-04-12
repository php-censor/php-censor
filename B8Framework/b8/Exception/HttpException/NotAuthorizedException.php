<?php

namespace b8\Exception\HttpException;
use b8\Exception\HttpException;

class NotAuthorizedException extends HttpException
{
	protected $errorCode = 401;
	protected $statusMessage = 'Not Authorized';
}