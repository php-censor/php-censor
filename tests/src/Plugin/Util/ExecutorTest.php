<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\DatabaseManager;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\Plugin\Util\Executor;
use PHPCensor\Plugin\Util\Factory;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildMetaStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use PHPCensor\Common\Application\ConfigurationInterface;

class ExecutorTest extends TestCase
{
    use ProphecyTrait;

    private Executor $testedExecutor;
    private BuildLogger $buildLogger;
    private Factory $factory;
    private BuildStore $buildStore;
    private ProjectStore $projectStore;
    private BuildMetaStore $buildMetaStore;
    private BuildErrorStore $buildErrorStore;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $this->projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->buildMetaStore = $this
            ->getMockBuilder(BuildMetaStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->buildErrorStore = $this
            ->getMockBuilder(BuildErrorStore::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->buildLogger = $this->prophesize(BuildLogger::class);
        $this->factory = $this->prophesize(Factory::class);

        $this->testedExecutor = new Executor(
            $this->factory->reveal(),
            $this->buildLogger->reveal(),
            $this->buildStore,
            $this->buildMetaStore,
            $this->buildErrorStore
        );
    }

    protected function getFakePluginClassName(string $pluginName): string
    {
        $pluginNamespace = 'Tests\\PHPCensor\\Plugin\\Util\\Fake\\';

        return $pluginNamespace . $pluginName;
    }

    public function testExecutePlugin_AssumesNamespaceIfNoneGiven(): void
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $pluginNamespace = 'PHPCensor\Plugin\\';

        $this->factory
            ->buildPlugin($pluginNamespace . $pluginName, $options)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize(Plugin::class)->reveal());

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_KeepsCalledNameSpace(): void
    {
        $options = [];
        $pluginClass = $this->getFakePluginClassName('ExamplePluginFull');

        $this->factory
            ->buildPlugin($pluginClass, $options)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize(Plugin::class)->reveal());

        $this->testedExecutor->executePlugin($pluginClass, $options);
    }

    public function testExecutePlugin_CallsExecuteOnFactoryBuildPlugin(): void
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $build = new Build($this->buildErrorStore, $this->buildStore, $this->projectStore);

        $plugin = $this->prophesize(Plugin::class);
        $plugin->execute()->shouldBeCalledTimes(1);

        $this->factory->buildPlugin(Argument::any(), Argument::any())->willReturn($plugin->reveal());
        $this->factory->getBuild()->willReturn($build);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_ReturnsPluginSuccess(): void
    {
        $options = [];
        $pluginName = 'PhpUnit';

        $expectedReturnValue = true;

        $plugin = $this->prophesize(Plugin::class);
        $plugin->execute()->willReturn($expectedReturnValue);

        $this->factory->buildPlugin(Argument::any(), Argument::any())->willReturn($plugin->reveal());

        $returnValue = $this->testedExecutor->executePlugin($pluginName, $options);

        self::assertEquals($expectedReturnValue, $returnValue);
    }

    public function testExecutePlugin_LogsFailureForNonExistentClasses(): void
    {
        $options    = [];
        $pluginName = 'DOESNTEXIST';

        $this->buildLogger->logFailure(\sprintf('Plugin does not exist: %s', $pluginName))->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_LogsFailureWhenExceptionsAreThrownByPlugin(): void
    {
        $options    = [];
        $pluginName = 'PhpUnit';

        $expectedException = new RuntimeException("Generic Error");

        $plugin = $this->prophesize(Plugin::class);
        $plugin->execute()->willThrow($expectedException);

        $this->factory->buildPlugin(Argument::any(), Argument::any())->willReturn($plugin->reveal());

        $this->buildLogger
            ->logFailure('Exception: ' . $expectedException->getMessage(), $expectedException)
            ->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugins_CallsEachPluginForStage(): void
    {
        $phpUnitPluginOptions = [];
        $behatPluginOptions   = [];
        $build                = new Build($this->buildErrorStore, $this->buildStore, $this->projectStore, ['id' => 1]);

        $config = [
            'stageOne' => [
                'PhpUnit' => $phpUnitPluginOptions,
                'Behat'   => $behatPluginOptions,
            ]
        ];

        $pluginNamespace = 'PHPCensor\Plugin\\';

        $phpUnitPlugin = $this->prophesize(Plugin::class);
        $phpUnitPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->factory
            ->buildPlugin($pluginNamespace . 'PhpUnit', $phpUnitPluginOptions)
            ->willReturn($phpUnitPlugin->reveal());
        $this->factory->getBuild()->willReturn($build);

        $behatPlugin = $this->prophesize(Plugin::class);
        $behatPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->factory
            ->buildPlugin($pluginNamespace . 'Behat', $behatPluginOptions)
            ->willReturn($behatPlugin->reveal());

        $this->testedExecutor->executePlugins($config, 'stageOne');
    }

    public function testGetBranchSpecificConfig(): void
    {
        $config = [
            'setup' => [
                'composer' => 'install',
            ]
        ];

        self::assertEquals([], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));

        $config = [
            'setup' => [
                'composer' => 'install',
            ],
            'branch-branch-1' => [
                'phpunit' => [],
            ],
        ];

        self::assertEquals(['phpunit' => []], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));

        $config = [
            'setup' => [
                'composer' => 'install',
            ],
            'branch-branch-2' => [
                'phpunit' => [],
            ],
        ];

        self::assertEquals([], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));

        $config = [
            'setup' => [
                'composer' => [
                    'install',
                ],
            ],
            'branch-regex:.+' => [
                'phpunit' => [],
            ],
        ];

        self::assertEquals(['phpunit' => []], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));

        $config = [
            'setup' => [
                'composer' => [
                    'install',
                ],
            ],
            'branch-regex:^branch\-\d$' => [
                'phpunit' => [],
            ],
        ];

        self::assertEquals(['phpunit' => []], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));

        $config = [
            'setup' => [
                'composer' => [
                    'install',
                ],
            ],
            'branch-regex:^branch\-\w{2,}$' => [
                'phpunit' => [],
            ],
        ];

        self::assertEquals([], $this->testedExecutor->getBranchSpecificConfig($config, 'branch-1'));
    }
}
