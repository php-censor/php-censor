<?php

namespace Tests\PHPCensor\Helper;

use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BuildInterpolatorTest extends TestCase
{
    use ProphecyTrait;

    protected BuildInterpolator $testedInterpolator;

    protected StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration   = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->testedInterpolator = new BuildInterpolator($this->storeRegistry);
    }

    public function testInterpolate_LeavesStringsUnchangedByDefault()
    {
        $string = "Hello World";
        $expectedOutput = "Hello World";

        $actualOutput = $this->testedInterpolator->interpolate($string);

        self::assertEquals($expectedOutput, $actualOutput);
    }

    public function testInterpolate_LeavesStringsUnchangedWhenBuildIsSet()
    {
        $build = $this->prophesize('PHPCensor\\Model\\Build')->reveal();

        $string         = "Hello World";
        $expectedOutput = "Hello World";

        $this->testedInterpolator->setupInterpolationVars(
            $build,
            'php-censor.local',
            '1.0.0'
        );

        $actualOutput = $this->testedInterpolator->interpolate($string);

        self::assertEquals($expectedOutput, $actualOutput);
    }
}
