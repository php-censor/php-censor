<?php

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\Factory;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\PHPCensor\Plugin\Util\Fake\ExamplePluginFull;
use Tests\PHPCensor\Plugin\Util\Fake\ExamplePluginWithSingleRequiredArg;
use Tests\PHPCensor\Plugin\Util\Fake\ExamplePluginWithSingleTypedRequiredArg;

class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    protected $testedFactory;

    protected $expectedResource;

    protected $resourceLoader;

    protected function setUp(): void
    {
        $this->testedFactory = new Factory();

        // Setup a resource that can be returned and asserted against
        $this->expectedResource = new stdClass();
        $resourceLink = $this->expectedResource;
        $this->resourceLoader = function () use (&$resourceLink) {
            return $resourceLink;
        };
    }

    protected function tearDown(): void
    {
        // Nothing to do.
    }

    public function testRegisterResourceThrowsExceptionWithoutTypeAndName()
    {
        self::expectException('\PHPCensor\Exception\InvalidArgumentException');
        self::expectExceptionMessage('Type or Name must be specified');
        $this->testedFactory->registerResource($this->resourceLoader, null, null);
    }

    public function testRegisterResourceThrowsExceptionIfLoaderIsntFunction()
    {
        self::expectException('\PHPCensor\Exception\InvalidArgumentException');
        self::expectExceptionMessage('$loader is expected to be a function');
        $this->testedFactory->registerResource(["dummy"], "TestName", "TestClass");
    }

    public function testBuildPluginWorksWithSingleOptionalArgConstructor()
    {
        $pluginClass = $this->getFakePluginClassName('ExamplePluginWithSingleOptionalArg');
        $plugin = $this->testedFactory->buildPlugin($pluginClass);
        self::assertInstanceOf($pluginClass, $plugin);
    }

    public function testBuildPluginThrowsExceptionIfMissingResourcesForRequiredArg()
    {
        self::expectException('\DomainException');
        self::expectExceptionMessage('Unsatisfied dependency: requiredArgument');

        $pluginClass = $this->getFakePluginClassName('ExamplePluginWithSingleRequiredArg');
        $this->testedFactory->buildPlugin($pluginClass);
    }

    public function testBuildPluginLoadsArgumentsBasedOnName()
    {
        $pluginClass = $this->getFakePluginClassName('ExamplePluginWithSingleRequiredArg');

        $this->testedFactory->registerResource(
            $this->resourceLoader,
            "requiredArgument"
        );

        /** @var ExamplePluginWithSingleRequiredArg $plugin */
        $plugin = $this->testedFactory->buildPlugin($pluginClass);

        self::assertEquals($this->expectedResource, $plugin->RequiredArgument);
    }

    public function testBuildPluginLoadsArgumentsBasedOnType()
    {
        $pluginClass = $this->getFakePluginClassName('ExamplePluginWithSingleTypedRequiredArg');

        $this->testedFactory->registerResource(
            $this->resourceLoader,
            null,
            "stdClass"
        );

        /** @var ExamplePluginWithSingleTypedRequiredArg $plugin */
        $plugin = $this->testedFactory->buildPlugin($pluginClass);

        self::assertEquals($this->expectedResource, $plugin->RequiredArgument);
    }

    public function testBuildPluginLoadsFullExample()
    {
        $pluginClass = $this->getFakePluginClassName('ExamplePluginFull');

        $this->registerBuildAndBuilder();

        /** @var ExamplePluginFull $plugin */
        $plugin = $this->testedFactory->buildPlugin($pluginClass);

        self::assertInstanceOf($pluginClass, $plugin);
    }

    public function testBuildPluginLoadsFullExampleWithOptions()
    {
        $pluginClass = $this->getFakePluginClassName('ExamplePluginFull');

        $expectedArgs = [
            'thing' => "stuff"
        ];

        $this->registerBuildAndBuilder();

        /** @var ExamplePluginFull $plugin */
        $plugin = $this->testedFactory->buildPlugin(
            $pluginClass,
            $expectedArgs
        );

        self::assertIsArray($plugin->options);
        self::assertArrayHasKey('thing', $plugin->options);
    }

    /**
     * Registers mocked Builder and Build classes so that realistic plugins
     * can be tested.
     */
    private function registerBuildAndBuilder()
    {
        $self = $this;

        $this->testedFactory->registerResource(
            function () use ($self) {
                return $self
                    ->getMockBuilder('PHPCensor\Builder')
                    ->disableOriginalConstructor()
                    ->getMock();
            },
            null,
            'PHPCensor\\Builder'
        );

        $this->testedFactory->registerResource(
            function () use ($self) {
                return $self
                    ->getMockBuilder('PHPCensor\Model\Build')
                    ->disableOriginalConstructor()
                    ->getMock();
            },
            null,
            'PHPCensor\\Model\\Build'
        );
    }

    protected function getFakePluginClassName($pluginName)
    {
        $pluginNamespace = '\\Tests\\PHPCensor\\Plugin\\Util\\Fake\\';

        return $pluginNamespace . $pluginName;
    }
}
