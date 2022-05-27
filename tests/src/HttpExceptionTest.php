<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use Exception;
use PHPCensor\Exception\HttpException;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    public function testHttpExceptionIsException(): void
    {
        $ex = new HttpException();
        self::assertTrue($ex instanceof Exception);
    }

    public function testHttpException(): void
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

    public function testBadRequestException(): void
    {
        try {
            throw new HttpException\BadRequestException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 400);
            self::assertTrue($ex->getStatusMessage() == 'Bad Request');
        }
    }

    public function testForbiddenException(): void
    {
        try {
            throw new HttpException\ForbiddenException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 403);
            self::assertTrue($ex->getStatusMessage() == 'Forbidden');
        }
    }

    public function testNotAuthorizedException(): void
    {
        try {
            throw new HttpException\NotAuthorizedException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 401);
            self::assertTrue($ex->getStatusMessage() == 'Not Authorized');
        }
    }

    public function testNotFoundException(): void
    {
        try {
            throw new HttpException\NotFoundException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 404);
            self::assertTrue($ex->getStatusMessage() == 'Not Found');
        }
    }

    public function testServerErrorException(): void
    {
        try {
            throw new HttpException\ServerErrorException('Test');
        } catch (HttpException $ex) {
            self::assertTrue($ex->getErrorCode() == 500);
            self::assertTrue($ex->getStatusMessage() == 'Internal Server Error');
        }
    }
}
