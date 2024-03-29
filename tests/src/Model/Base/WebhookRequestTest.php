<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\ProjectGroup;
use PHPCensor\Model\Base\WebhookRequest;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class WebhookRequestTest extends TestCase
{
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct(): void
    {
        $webhookRequest = new WebhookRequest($this->storeRegistry);

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
        $webhookRequest = new WebhookRequest($this->storeRegistry);

        $result = $webhookRequest->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $webhookRequest->getId());

        $result = $webhookRequest->setId(100);
        self::assertEquals(false, $result);
    }

    public function testProjectId(): void
    {
        $webhookRequest = new WebhookRequest($this->storeRegistry);

        $result = $webhookRequest->setProjectId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $webhookRequest->getProjectId());

        $result = $webhookRequest->setProjectId(200);
        self::assertEquals(false, $result);
    }

    public function testWebhookType(): void
    {
        $webhookRequest = new WebhookRequest($this->storeRegistry);

        $result = $webhookRequest->setWebhookType('git');
        self::assertEquals(true, $result);
        self::assertEquals('git', $webhookRequest->getWebhookType());

        $result = $webhookRequest->setWebhookType('git');
        self::assertEquals(false, $result);
    }

    public function testPayload(): void
    {
        $webhookRequest = new WebhookRequest($this->storeRegistry);

        $result = $webhookRequest->setPayload('payload');
        self::assertEquals(true, $result);
        self::assertEquals('payload', $webhookRequest->getPayload());

        $result = $webhookRequest->setPayload('payload');
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);
        self::assertEquals(null, $projectGroup->getCreateDate());

        $projectGroup = new ProjectGroup($this->storeRegistry);
        $createDate   = new DateTime();

        $result = $projectGroup->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $projectGroup->getCreateDate()->getTimestamp());

        $result = $projectGroup->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $projectGroup = new ProjectGroup($this->storeRegistry, ['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $projectGroup->getCreateDate()->getTimestamp());

        $projectGroup = new ProjectGroup($this->storeRegistry, ['create_date' => 'Invalid Data']);
        self::assertNull($projectGroup->getCreateDate());
    }
}
