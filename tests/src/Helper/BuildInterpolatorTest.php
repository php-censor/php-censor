<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\DatabaseManager;
use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Model\Build;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\SecretStore;
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
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $secretStore = $this
            ->getMockBuilder(SecretStore::class)
            ->setConstructorArgs([$databaseManager, $this->storeRegistry])
            ->getMock();

        $environmentStore = $this
            ->getMockBuilder(EnvironmentStore::class)
            ->setConstructorArgs([$databaseManager, $this->storeRegistry])
            ->getMock();

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
        $build = new Build($this->storeRegistry);

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
