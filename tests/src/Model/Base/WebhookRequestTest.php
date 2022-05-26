<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Model;
use PHPCensor\Model\Base\ProjectGroup;
use PHPCensor\Model\Base\WebhookRequest;
use PHPUnit\Framework\TestCase;

class WebhookRequestTest extends TestCase
{
    public function testConstruct(): void
    {
        $webhookRequest = new WebhookRequest();

        self::assertInstanceOf(Model::class, $webhookRequest);
        self::assertInstanceOf(WebhookRequest::class, $webhookRequest);

        self::assertEquals([
            'id'           => null,
            'project_id'   => null,
            'webhook_type' => null,
            'payload'      => null,
            'create_date'  => null,
        ], $webhookRequest->getDataArray());
    }

    public function testId(): void
    {
        $webhookRequest = new WebhookRequest();

        $result = $webhookRequest->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $webhookRequest->getId());

        $result = $webhookRequest->setId(100);
        self::assertEquals(false, $result);
    }

    public function testProjectId(): void
    {
        $webhookRequest = new WebhookRequest();

        $result = $webhookRequest->setProjectId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $webhookRequest->getProjectId());

        $result = $webhookRequest->setProjectId(200);
        self::assertEquals(false, $result);
    }

    public function testWebhookType(): void
    {
        $webhookRequest = new WebhookRequest();

        $result = $webhookRequest->setWebhookType('git');
        self::assertEquals(true, $result);
        self::assertEquals('git', $webhookRequest->getWebhookType());

        $result = $webhookRequest->setWebhookType('git');
        self::assertEquals(false, $result);
    }

    public function testPayload(): void
    {
        $webhookRequest = new WebhookRequest();

        $result = $webhookRequest->setPayload('payload');
        self::assertEquals(true, $result);
        self::assertEquals('payload', $webhookRequest->getPayload());

        $result = $webhookRequest->setPayload('payload');
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $projectGroup = new ProjectGroup();
        self::assertEquals(null, $projectGroup->getCreateDate());

        $projectGroup = new ProjectGroup();
        $createDate   = new DateTime();

        $result = $projectGroup->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $projectGroup->getCreateDate()->getTimestamp());

        $result = $projectGroup->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $projectGroup = new ProjectGroup(['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $projectGroup->getCreateDate()->getTimestamp());

        $projectGroup = new ProjectGroup(['create_date' => 'Invalid Data']);
        self::assertNull($projectGroup->getCreateDate());
    }
}
