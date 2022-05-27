<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Model\Build;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use PHPCensor\Common\Application\ConfigurationInterface;

class BuildInterpolatorTest extends TestCase
{
    use ProphecyTrait;

    private BuildInterpolator $testedInterpolator;

    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
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

    public function testInterpolate_LeavesStringsUnchangedByDefault(): void
    {
        $string = "Hello World";
        $expectedOutput = "Hello World";

        $actualOutput = $this->testedInterpolator->interpolate($string);

        self::assertEquals($expectedOutput, $actualOutput);
    }

    public function testInterpolate_LeavesStringsUnchangedWhenBuildIsSet(): void
    {
        /** @var Build $build */
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
