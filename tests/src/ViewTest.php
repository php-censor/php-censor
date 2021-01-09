<?php

namespace Tests\PHPCensor;

use Exception;
use PHPCensor\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testSimpleView()
    {
        $view = new View('simple', ROOT_DIR . 'tests/data/View/');
        self::assertTrue($view->render() == 'Hello');
    }

    public function testInvalidView()
    {
        self::expectException(Exception::class);

        new View('dogs', ROOT_DIR . 'tests/data/View/');
    }

    public function testViewVars()
    {
        $view = new View('vars', ROOT_DIR . 'tests/data/View/');
        $view->who = 'World';

        self::assertTrue(isset($view->who));
        self::assertFalse(isset($view->what));
        self::assertTrue($view->render() == 'Hello World');
    }
}
