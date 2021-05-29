<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\PhpUnit;
use PHPCensor\Plugin\Util\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testConstructor()
    {
        $builder = $this
            ->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $build = $this
            ->getMockBuilder(Build::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Factory($builder, $build);

        self::assertInstanceOf(Factory::class, $factory);
    }

    public function testGetBuild()
    {
        $builder = $this
            ->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $build = $this
            ->getMockBuilder(Build::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Factory($builder, $build);

        self::assertInstanceOf(Build::class, $factory->getBuild());
    }

    public function testBuildPlugin()
    {
        $builder = $this
            ->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $build = $this
            ->getMockBuilder(Build::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Factory($builder, $build);

        self::assertInstanceOf(PhpUnit::class, $factory->buildPlugin(PhpUnit::class, []));
    }
}
