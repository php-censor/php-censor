<?php

namespace b8\Exception\HttpException;
use b8\Exception\HttpException;

class BadRequestException extends HttpException
{
	protected $errorCode = 400;
	protected $statusMessage = 'Bad Request';
}