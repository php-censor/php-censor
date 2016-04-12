<?php

namespace b8\Exception\HttpException;
use b8\Exception\HttpException;

class ForbiddenException extends HttpException
{
	protected $errorCode = 403;
	protected $statusMessage = 'Forbidden';
}