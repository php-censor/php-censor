<?php

namespace Tests\b8;

use b8\Exception\HttpException;

class HttpExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testHttpExceptionIsException()
    {
        $ex = new HttpException();
        self::assertTrue($ex instanceof \Exception);
    }

    public function testHttpException()
    {
        try {
            throw new HttpException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getMessage() == 'Test');
            self::assertTrue($ex->getErrorCode() == 500);
            self::assertTrue($ex->getStatusMessage() == 'Internal Server Error');
            self::assertTrue($ex->getHttpHeader() == 'HTTP/1.1 500 Internal Server Error');
        }
    }

    public function testBadRequestException()
    {
        try {
            throw new HttpException\BadRequestException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 400);
            self::assertTrue($ex->getStatusMessage() == 'Bad Request');
        }
    }

    public function testForbiddenException()
    {
        try {
            throw new HttpException\ForbiddenException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 403);
            self::assertTrue($ex->getStatusMessage() == 'Forbidden');
        }
    }

    public function testNotAuthorizedException()
    {
        try {
            throw new HttpException\NotAuthorizedException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 401);
            self::assertTrue($ex->getStatusMessage() == 'Not Authorized');
        }
    }

    public function testNotFoundException()
    {
        try {
            throw new HttpException\NotFoundException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 404);
            self::assertTrue($ex->getStatusMessage() == 'Not Found');
        }
    }

    public function testServerErrorException()
    {
        try {
            throw new HttpException\ServerErrorException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 500);
            self::assertTrue($ex->getStatusMessage() == 'Internal Server Error');
        }
    }

    public function testValidationException()
    {
        try {
            throw new HttpException\ValidationException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 400);
            self::assertTrue($ex->getStatusMessage() == 'Bad Request');
        }
    }
}
