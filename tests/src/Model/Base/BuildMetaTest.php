<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model\Base\BuildMeta;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class BuildMetaTest extends TestCase
{
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct(): void
    {
        $buildMeta = new BuildMeta($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $buildMeta);
        self::assertInstanceOf('PHPCensor\Model\Base\BuildMeta', $buildMeta);

        self::assertEquals([
            'id'         => null,
            'build_id'   => null,
            'meta_key'   => null,
            'meta_value' => null,
        ], $buildMeta->getDataArray());
    }

    public function testId(): void
    {
        $buildMeta = new BuildMeta($this->storeRegistry);

        $result = $buildMeta->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $buildMeta->getId());

        $result = $buildMeta->setId(100);
        self::assertEquals(false, $result);
    }

    public function testBuildId(): void
    {
        $buildMeta = new BuildMeta($this->storeRegistry);

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $buildMeta->getBuildId());

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(false, $result);
    }

    public function testMetaKey(): void
    {
        $buildMeta = new BuildMeta($this->storeRegistry);

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(true, $result);
        self::assertEquals('key', $buildMeta->getMetaKey());

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(false, $result);
    }

    public function testMetaValue(): void
    {
        $buildMeta = new BuildMeta($this->storeRegistry);

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(true, $result);
        self::assertEquals('value', $buildMeta->getMetaValue());

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(false, $result);
    }
}
