<?php

require_once(dirname(__FILE__) . '/../b8/Registry.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/BadRequestException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/ForbiddenException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/NotAuthorizedException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/NotFoundException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/ServerErrorException.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException/ValidationException.php');

use b8\Exception\HttpException,
	b8\Exception\HttpException\BadRequestException;

class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
	public function testHttpExceptionIsException()
	{
		$ex = new HttpException();
		$this->assertTrue($ex instanceof \Exception);
	}

	public function testHttpException()
	{
		try
		{
			throw new HttpException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getMessage() == 'Test');
			$this->assertTrue($ex->getErrorCode() == 500);
			$this->assertTrue($ex->getStatusMessage() == 'Internal Server Error');
			$this->assertTrue($ex->getHttpHeader() == 'HTTP/1.1 500 Internal Server Error');
		}
	}

	public function testBadRequestException()
	{
		try
		{
			throw new HttpException\BadRequestException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 400);
			$this->assertTrue($ex->getStatusMessage() == 'Bad Request');
		}
	}

	public function testForbiddenException()
	{
		try
		{
			throw new HttpException\ForbiddenException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 403);
			$this->assertTrue($ex->getStatusMessage() == 'Forbidden');
		}
	}

	public function testNotAuthorizedException()
	{
		try
		{
			throw new HttpException\NotAuthorizedException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 401);
			$this->assertTrue($ex->getStatusMessage() == 'Not Authorized');
		}
	}

	public function testNotFoundException()
	{
		try
		{
			throw new HttpException\NotFoundException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 404);
			$this->assertTrue($ex->getStatusMessage() == 'Not Found');
		}
	}

	public function testServerErrorException()
	{
		try
		{
			throw new HttpException\ServerErrorException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 500);
			$this->assertTrue($ex->getStatusMessage() == 'Internal Server Error');
		}
	}

	public function testValidationException()
	{
		try
		{
			throw new HttpException\ValidationException('Test');
		}
		catch(HttpException $ex)
		{
			$this->assertTrue($ex->getErrorCode() == 400);
			$this->assertTrue($ex->getStatusMessage() == 'Bad Request');
		}
	}
}