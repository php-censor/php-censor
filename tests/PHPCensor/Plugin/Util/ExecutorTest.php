<?php

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\Executor;
use Prophecy\Argument;

class ExecutorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Executor
     */
    protected $testedExecutor;

    protected $mockBuildLogger;

    protected $mockFactory;

    protected $mockStore;

    protected function setUp()
    {
        parent::setUp();
        $this->mockBuildLogger = $this->prophesize('\PHPCensor\Logging\BuildLogger');
        $this->mockFactory = $this->prophesize('\PHPCensor\Plugin\Util\Factory');
        $this->mockStore = $this->prophesize('\PHPCensor\Store\BuildStore');
        $this->testedExecutor = new Executor(
            $this->mockFactory->reveal(),
            $this->mockBuildLogger->reveal(),
            $this->mockStore->reveal()
        );
    }

    protected function getFakePluginClassName($pluginName)
    {
        $pluginNamespace = '\\Tests\\PHPCensor\\Plugin\\Util\\Fake\\';

        return $pluginNamespace . $pluginName;
    }

    public function testExecutePlugin_AssumesNamespaceIfNoneGiven()
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $pluginNamespace = 'PHPCensor\\Plugin\\';

        $this->mockFactory->buildPlugin($pluginNamespace . $pluginName, $options)
                          ->shouldBeCalledTimes(1)
                          ->willReturn($this->prophesize('PHPCensor\Plugin')->reveal());

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_KeepsCalledNameSpace()
    {
        $options = [];
        $pluginClass = $this->getFakePluginClassName('ExamplePluginFull');

        $this->mockFactory->buildPlugin($pluginClass, $options)
          ->shouldBeCalledTimes(1)
          ->willReturn($this->prophesize('PHPCensor\Plugin')->reveal());

        $this->testedExecutor->executePlugin($pluginClass, $options);
    }

    public function testExecutePlugin_CallsExecuteOnFactoryBuildPlugin()
    {
        $options = [];
        $pluginName = 'PhpUnit';
        $build = new \PHPCensor\Model\Build();

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->execute()->shouldBeCalledTimes(1);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());
        $this->mockFactory->getResourceFor('PHPCensor\Model\Build')->willReturn($build);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_ReturnsPluginSuccess()
    {
        $options = [];
        $pluginName = 'PhpUnit';

        $expectedReturnValue = true;

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->execute()->willReturn($expectedReturnValue);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());

        $returnValue = $this->testedExecutor->executePlugin($pluginName, $options);

        self::assertEquals($expectedReturnValue, $returnValue);
    }

    public function testExecutePlugin_LogsFailureForNonExistentClasses()
    {
        $options    = [];
        $pluginName = 'DOESNTEXIST';

        $this->mockBuildLogger->logFailure(sprintf('Plugin does not exist: %s', $pluginName))->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugin_LogsFailureWhenExceptionsAreThrownByPlugin()
    {
        $options    = [];
        $pluginName = 'PhpUnit';

        $expectedException = new \RuntimeException("Generic Error");

        $mockPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPlugin->execute()->willThrow($expectedException);

        $this->mockFactory->buildPlugin(Argument::any(), Argument::any())->willReturn($mockPlugin->reveal());

        $this->mockBuildLogger->logFailure('Exception: ' . $expectedException->getMessage(), $expectedException)
                              ->shouldBeCalledTimes(1);

        $this->testedExecutor->executePlugin($pluginName, $options);
    }

    public function testExecutePlugins_CallsEachPluginForStage()
    {
        $phpUnitPluginOptions = [];
        $behatPluginOptions   = [];
        $build                = new \PHPCensor\Model\Build();

        $config = [
           'stageOne' => [
               'PhpUnit' => $phpUnitPluginOptions,
               'Behat'   => $behatPluginOptions,
           ]
        ];

        $pluginNamespace = 'PHPCensor\\Plugin\\';

        $mockPhpUnitPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockPhpUnitPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->mockFactory->buildPlugin($pluginNamespace . 'PhpUnit', $phpUnitPluginOptions)
                          ->willReturn($mockPhpUnitPlugin->reveal());
        $this->mockFactory->getResourceFor('PHPCensor\Model\Build')->willReturn($build);

        $mockBehatPlugin = $this->prophesize('PHPCensor\Plugin');
        $mockBehatPlugin->execute()->shouldBeCalledTimes(1)->willReturn(true);

        $this->mockFactory->buildPlugin($pluginNamespace . 'Behat', $behatPluginOptions)
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

