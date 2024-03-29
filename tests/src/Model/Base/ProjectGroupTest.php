<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\ProjectGroup;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class ProjectGroupTest extends TestCase
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
        $projectGroup = new ProjectGroup($this->storeRegistry);

        self::assertInstanceOf(Model::class, $projectGroup);
        self::assertInstanceOf(ProjectGroup::class, $projectGroup);

        self::assertEquals([
            'id'          => null,
            'title'       => null,
            'create_date' => null,
            'user_id'     => null,
        ], $projectGroup->getDataArray());
    }

    public function testId(): void
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $projectGroup->getId());

        $result = $projectGroup->setId(100);
        self::assertEquals(false, $result);
    }

    public function testTitle(): void
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setTitle('title');
        self::assertEquals(true, $result);
        self::assertEquals('title', $projectGroup->getTitle());

        $result = $projectGroup->setTitle('title');
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

    public function testUserId(): void
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setUserId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $projectGroup->getUserId());

        $result = $projectGroup->setUserId(200);
        self::assertEquals(false, $result);
    }
}
