<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Configuration;
use PHPCensor\Controller;
use PHPCensor\DatabaseManager;
use PHPCensor\Http\Request;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class TestController extends Controller
{
    public function init(): void
    {
    }
}

class ControllerTest extends TestCase
{
    private Request $request;
    private Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration = new Configuration('');

        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->getMock();

        $this->controller = new TestController($configuration, $storeRegistry, $this->request);
    }

    public function testConstruct()
    {
        self::assertInstanceOf(Controller::class, $this->controller);
    }

    public function testGetParams()
    {
        $this->request
            ->expects($this->once())
            ->method('getParams')
            ->willReturn(['param' => 'value']);

        self::assertEquals(['param' => 'value'], $this->controller->getParams());
    }

    public function testGetParam()
    {
        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('param2')
            ->willReturn('value2');

        self::assertEquals('value2', $this->controller->getParam('param2'));
    }

    public function testSetParam()
    {
        $this->request
            ->expects($this->once())
            ->method('setParam')
            ->with('param3', 'value3');

        $this->controller->setParam('param3', 'value3');
    }

    public function testUnsetParam()
    {
        $this->request
            ->expects($this->once())
            ->method('unsetParam')
            ->with('param4');

        $this->controller->unsetParam('param4');
    }

    public function testHandleAction()
    {
        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('param5');

        $this->controller->handleAction('getParam', ['param5']);
    }

    public function testHasActionSuccess()
    {
        self::assertEquals(true, $this->controller->hasAction('getParam'));
    }

    public function testHasActionFailed()
    {
        self::assertEquals(false, $this->controller->hasAction('getParamNew'));
    }
}
