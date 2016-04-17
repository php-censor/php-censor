<?php

namespace b8\Exception\HttpException;
use b8\Exception\HttpException;

class NotFoundException extends HttpException
{
	protected $errorCode = 404;
	protected $statusMessage = 'Not Found';
}