<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Configuration;
use PHPCensor\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;

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

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->getMock();

        $this->controller = new TestController(
            $configuration,
            $this->request,
            new Session()
        );
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(Controller::class, $this->controller);
    }

    public function testGetParam(): void
    {
        $this->request
            ->expects($this->once())
            ->method('get')
            ->with('param2')
            ->willReturn('value2');

        self::assertEquals('value2', $this->controller->getParam('param2'));
    }

    public function testHandleAction(): void
    {
        $this->request
            ->expects($this->once())
            ->method('get')
            ->with('param5');

        $this->controller->handleAction('getParam', ['param5']);
    }

    public function testHasActionSuccess(): void
    {
        self::assertEquals(true, $this->controller->hasAction('getParam'));
    }

    public function testHasActionFailed(): void
    {
        self::assertEquals(false, $this->controller->hasAction('getParamNew'));
    }
}
