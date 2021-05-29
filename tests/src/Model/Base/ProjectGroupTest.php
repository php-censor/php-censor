<?php

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Model\Base\ProjectGroup;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class ProjectGroupTest extends TestCase
{
    protected StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct()
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $projectGroup);
        self::assertInstanceOf('PHPCensor\Model\Base\ProjectGroup', $projectGroup);

        self::assertEquals([
            'id'          => null,
            'title'       => null,
            'create_date' => null,
            'user_id'     => null,
        ], $projectGroup->getDataArray());
    }

    public function testId()
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $projectGroup->getId());

        $result = $projectGroup->setId(100);
        self::assertEquals(false, $result);
    }

    public function testTitle()
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setTitle('title');
        self::assertEquals(true, $result);
        self::assertEquals('title', $projectGroup->getTitle());

        $result = $projectGroup->setTitle('title');
        self::assertEquals(false, $result);
    }

    public function testCreateDate()
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
    }

    public function testUserId()
    {
        $projectGroup = new ProjectGroup($this->storeRegistry);

        $result = $projectGroup->setUserId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $projectGroup->getUserId());

        $result = $projectGroup->setUserId(200);
        self::assertEquals(false, $result);
    }
}
