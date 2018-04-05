<?php

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model\Base\BuildMeta;
use PHPUnit\Framework\TestCase;

class BuildMetaTest extends TestCase
{
    public function testConstruct()
    {
        $buildMeta = new BuildMeta();

        self::assertInstanceOf('PHPCensor\Model', $buildMeta);
        self::assertInstanceOf('PHPCensor\Model\Base\BuildMeta', $buildMeta);

        self::assertEquals([
            'id'         => null,
            'build_id'   => null,
            'meta_key'   => null,
            'meta_value' => null,
        ], $buildMeta->getDataArray());
    }

    public function testId()
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $buildMeta->getId());

        $result = $buildMeta->setId(100);
        self::assertEquals(false, $result);
    }

    public function testBuildId()
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $buildMeta->getBuildId());

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(false, $result);
    }

    public function testMetaKey()
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(true, $result);
        self::assertEquals('key', $buildMeta->getMetaKey());

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(false, $result);
    }

    public function testMetaValue()
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(true, $result);
        self::assertEquals('value', $buildMeta->getMetaValue());

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(false, $result);
    }
}
