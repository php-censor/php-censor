<?php

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\Util\Executor;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class ExecutorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Executor
     */
    protected $testedExecutor;

    protected $mockBuildLogger;

    protected $mockFactory;

    protected $mockStore;

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

        $this->mockBuildLogger = $this->prophesize('\PHPCensor\Logging\BuildLogger');
        $this->mockFactory = $this->prophesize('\PHPCensor\Plugin\Util\Factory');
        $this->mockStore = $this->prophesize('\PHPCensor\Store\BuildStore');
        $this->testedExecutor = new Executor(
            $this->storeRegistry,
            $this->mockFactory->reveal(),
            $this->mockBuildLogger->reveal(),
            $this->mockStore->reveal()
        );
    }

    protected function getFakePluginClassName($pluginName)
    {
        $pluginNamespace = 'Tests\\PHPCensor\\Plugin\\Util\\Fake\\';

        return $pluginNamespace . $pluginName;
    }

    public function testExecutePlugin_AssumesNamespaceIfNoneGiven()
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $pluginNamespace = 'PHPCensor\\Plugin\\';

        $this->mockFactory
            ->buildPlugin($pluginNamespace . $pluginName, $options)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize('PHPCensor\Plugin')->reveal());

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_KeepsCalledNameSpace()
    {
        $options = [];
        $pluginClass = $this->getFakePluginClassName('ExamplePluginFull');

        $this->mockFactory
            ->buildPlugin($pluginClass, $options)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize('PHPCensor\Plugin')->reveal());

        $this->testedExecutor->executePlugin($pluginClass, $options);
    }

    public function testExecutePlugin_CallsExecuteOnFactoryBuildPlugin()
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $build = new Build($this->storeRegistry);

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->setStoreRegistry($this->storeRegistry)->shouldBeCalledTimes(1);
        $mockPlugin->execute()->shouldBeCalledTimes(1);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());
        $this->mockFactory->getBuild()->willReturn($build);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_ReturnsPluginSuccess()
    {
        $options = [];
        $pluginName = 'PhpUnit';

        $expectedReturnValue = true;

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->setStoreRegistry($this->storeRegistry)->shouldBeCalledTimes(1);
        $mockPlugin->execute()->willReturn($expectedReturnValue);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());

        $returnValue = $this->testedExecutor->executePlugin($pluginName, $options);

        self::assertEquals($expectedReturnValue, $returnValue);
    }

    public function testExecutePlugin_LogsFailureForNonExistentClasses()
    {
        $options    = [];
        $pluginName = 'DOESNTEXIST';

        $this->mockBuildLogger->logFailure(\sprintf('Plugin does not exist: %s', $pluginName))->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_LogsFailureWhenExceptionsAreThrownByPlugin()
    {
        $options    = [];
        $pluginName = 'PhpUnit';

        $expectedException = new RuntimeException("Generic Error");

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->setStoreRegistry($this->storeRegistry)->shouldBeCalledTimes(1);
        $mockPlugin->execute()->willThrow($expectedException);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());

        $this->mockBuildLogger
            ->logFailure('Exception: ' . $expectedException->getMessage(), $expectedException)
            ->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugins_CallsEachPluginForStage()
    {
        $phpUnitPluginOptions = [];
        $behatPluginOptions   = [];
        $build                = new Build($this->storeRegistry);

        $config = [
            'stageOne' => [
                'PhpUnit' => $phpUnitPluginOptions,
                'Behat'   => $behatPluginOptions,
            ]
        ];

        $pluginNamespace = 'PHPCensor\\Plugin\\';

        $mockPhpUnitPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPhpUnitPlugin->setStoreRegistry($this->storeRegistry)->shouldBeCalledTimes(1);
        $mockPhpUnitPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->mockFactory
            ->buildPlugin($pluginNamespace . 'PhpUnit', $phpUnitPluginOptions)
            ->willReturn($mockPhpUnitPlugin->reveal());
        $this->mockFactory->getBuild()->willReturn($build);

        $mockBehatPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockBehatPlugin->setStoreRegistry($this->storeRegistry)->shouldBeCalledTimes(1);
        $mockBehatPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->mockFactory
            ->buildPlugin($pluginNamespace . 'Behat', $behatPluginOptions)
            ->willReturn($mockBehatPlugin->reveal());

        $this->testedExecutor->executePlugins($config, 'stageOne');
    }

    public function testGetBranchSpecificConfig()
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
