<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model;
use PHPCensor\Model\Base\BuildMeta;
use PHPUnit\Framework\TestCase;

class BuildMetaTest extends TestCase
{
    public function testConstruct(): void
    {
        $buildMeta = new BuildMeta();

        self::assertInstanceOf(Model::class, $buildMeta);
        self::assertInstanceOf(BuildMeta::class, $buildMeta);

        self::assertEquals([
            'id'         => null,
            'build_id'   => null,
            'meta_key'   => null,
            'meta_value' => null,
        ], $buildMeta->getDataArray());
    }

    public function testId(): void
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $buildMeta->getId());

        $result = $buildMeta->setId(100);
        self::assertEquals(false, $result);
    }

    public function testBuildId(): void
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $buildMeta->getBuildId());

        $result = $buildMeta->setBuildId(200);
        self::assertEquals(false, $result);
    }

    public function testMetaKey(): void
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(true, $result);
        self::assertEquals('key', $buildMeta->getMetaKey());

        $result = $buildMeta->setMetaKey('key');
        self::assertEquals(false, $result);
    }

    public function testMetaValue(): void
    {
        $buildMeta = new BuildMeta();

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(true, $result);
        self::assertEquals('value', $buildMeta->getMetaValue());

        $result = $buildMeta->setMetaValue('value');
        self::assertEquals(false, $result);
    }
}
