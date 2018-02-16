<?php

namespace Tests\b8;

use PHPCensor\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testSimpleView()
    {
        $view = new View('simple', __DIR__ . '/data/view/');
        self::assertTrue($view->render() == 'Hello');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidView()
    {
        new View('dogs', __DIR__ . '/data/view/');
    }

    public function testViewVars()
    {
        $view = new View('vars', __DIR__ . '/data/view/');
        $view->who = 'World';

        self::assertTrue(isset($view->who));
        self::assertFalse(isset($view->what));
        self::assertTrue($view->render() == 'Hello World');
    }

    public function testUserViewVars()
    {
        $view          = new View('{@content}');
        $view->content = 'World';
        self::assertTrue($view->render() == 'World');
    }
}
