<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\DatabaseManager;
use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Model\Build;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use PHPCensor\Common\Application\ConfigurationInterface;

class BuildInterpolatorTest extends TestCase
{
    use ProphecyTrait;

    private BuildInterpolator $testedInterpolator;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $secretStore      = new SecretStore($databaseManager, $storeRegistry);
        $environmentStore = new EnvironmentStore($databaseManager, $storeRegistry);

        $this->testedInterpolator = new BuildInterpolator($environmentStore, $secretStore);
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
        $build = $this->prophesize(Build::class)->reveal();

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
