<?php

namespace Tests\PHPCensor\Controller;

use PHPCensor\Controller\WebhookController;
use PHPUnit\Framework\TestCase;

class WebhookControllerTest extends TestCase
{
    public function test_wrong_action_name_return_json_with_error()
    {
        $webController = new WebhookController(
            $this->prophesize('PHPCensor\Config')->reveal(),
            $this->prophesize('Symfony\Component\HttpFoundation\Request')->reveal(),
            $this->prophesize('PHPCensor\Http\Response')->reveal()
        );

        $error = $webController->handleAction('test', []);

        self::assertInstanceOf('PHPCensor\Http\Response\JsonResponse', $error);

        $responseData = $error->getData();
        self::assertEquals(500, $responseData['code']);

        self::assertEquals('failed', $responseData['body']['status']);

        self::assertEquals('application/json', $responseData['headers']['Content-Type']);
    }
}
