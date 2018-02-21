<?php

namespace Tests\PHPCensor\Plugin\Helper;

use PHPCensor\Helper\BuildInterpolator;

class BuildInterpolatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BuildInterpolator
     */
    protected $testedInterpolator;

    protected function setUp()
    {
        parent::setup();
        $this->testedInterpolator = new BuildInterpolator();
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
            "/buildpath/",
            "php-censor.local"
        );

        $actualOutput = $this->testedInterpolator->interpolate($string);

        self::assertEquals($expectedOutput, $actualOutput);
    }
}

