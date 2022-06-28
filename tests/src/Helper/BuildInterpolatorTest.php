<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\DatabaseManager;
use PHPCensor\Helper\VariableInterpolator;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use PHPCensor\Common\Application\ConfigurationInterface;

class BuildInterpolatorTest extends TestCase
{
    use ProphecyTrait;

    private VariableInterpolator $testedInterpolator;

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

        $build = $this->createMock(Build::class);
        $build
            ->method('getProject')
            ->willReturn(new Project($this->storeRegistry));

        $this->testedInterpolator = new VariableInterpolator(
            $build,
            $build->getProject(),
            $environmentStore,
            $secretStore,
            '1.0.0'
        );
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
        $string         = "Hello World";
        $expectedOutput = "Hello World";

        $actualOutput = $this->testedInterpolator->interpolate($string);

        self::assertEquals($expectedOutput, $actualOutput);
    }
}
