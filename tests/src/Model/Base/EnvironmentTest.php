<?php

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model\Base\Environment;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
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
        $environment = new Environment($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $environment);
        self::assertInstanceOf('PHPCensor\Model\Base\Environment', $environment);

        self::assertEquals([
            'id'         => null,
            'project_id' => null,
            'name'       => null,
            'branches'   => null,
        ], $environment->getDataArray());
    }

    public function testId()
    {
        $environment = new Environment($this->storeRegistry);

        $result = $environment->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $environment->getId());

        $result = $environment->setId(100);
        self::assertEquals(false, $result);
    }

    public function testProjectId()
    {
        $environment = new Environment($this->storeRegistry);

        $result = $environment->setProjectId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $environment->getProjectId());

        $result = $environment->setProjectId(200);
        self::assertEquals(false, $result);
    }

    public function testName()
    {
        $environment = new Environment($this->storeRegistry);

        $result = $environment->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $environment->getName());

        $result = $environment->setName('name');
        self::assertEquals(false, $result);
    }

    public function testBranches()
    {
        $environment = new Environment($this->storeRegistry);

        $result = $environment->setBranches(['branch-1', 'branch-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['branch-1', 'branch-2'], $environment->getBranches());

        $result = $environment->setBranches(['branch-1', 'branch-2']);
        self::assertEquals(false, $result);
    }
}
