<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Configuration;
use PHPCensor\Controller;
use PHPCensor\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;

class TestController extends Controller
{
    public function init(): void
    {
    }

    public function data(): array
    {
        return ['status' => 'ok'];
    }
}

class ControllerTest extends TestCase
{
    private Controller $controller;
    private StoreRegistry $storeRegistry;
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $configuration = new Configuration('');

        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();

        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->controller = $this->createController(new Request());
    }

    private function createController(Request $request): Controller
    {
        return new TestController($this->configuration, $this->storeRegistry, $request, new Session());
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(Controller::class, $this->controller);
    }

    public function testGetParam(): void
    {
        $controller = $this->createController(new Request(['param2' => 'value2']));

        self::assertEquals('value2', $controller->getParam('param2'));
        self::assertNull($controller->getParam('param3'));
        self::assertEquals('default', $controller->getParam('param3', 'default'));
    }

    public function testGetParamLookupOrder(): void
    {
        $request = new Request(
            ['from_query' => 'query', 'both' => 'query', 'all' => 'query'],
            ['from_body' => 'body', 'both' => 'body', 'all' => 'body'],
            ['all' => 'attribute']
        );
        $controller = $this->createController($request);

        self::assertEquals('query', $controller->getParam('from_query'));
        self::assertEquals('body', $controller->getParam('from_body'));
        self::assertEquals('query', $controller->getParam('both'));
        self::assertEquals('attribute', $controller->getParam('all'));
    }

    public function testGetParamWithArrayValue(): void
    {
        $controller = $this->createController(new Request([], ['groups' => ['1', '2']]));

        self::assertEquals(['1', '2'], $controller->getParam('groups'));
    }

    public function testHandleAction(): void
    {
        $controller = $this->createController(new Request(['param5' => 'value5']));

        self::assertEquals('value5', $controller->handleAction('getParam', ['param5']));
    }

    public function testHandleActionReturningArray(): void
    {
        // Webhook actions return arrays (see WebhookController::handleAction()).
        self::assertEquals(['status' => 'ok'], $this->controller->handleAction('data', []));
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
