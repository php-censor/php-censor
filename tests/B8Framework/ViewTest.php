<?php

namespace Tests\b8;

use b8\View;
use b8\View\Template;

class ViewTest extends \PHPUnit\Framework\TestCase
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

    /**
     * @expectedException \Exception
     */
    public function testInvalidHelper()
    {
        $view = new Template('{@Invalid:test}');
        $view->render();
    }

    public function testSimpleUserView()
    {
        $view = new Template('Hello');
        self::assertTrue($view->render() == 'Hello');
    }

    public function testUserViewYear()
    {
        $view = new Template('{@year}');
        self::assertTrue($view->render() == date('Y'));
    }

    public function testUserViewVars()
    {
        $view      = new Template('Hello {@who}');
        $view->who = 'World';
        self::assertTrue($view->render() == 'Hello World');

        $view = new Template('Hello {@who}');
        self::assertTrue($view->render() == 'Hello ');

        $view      = new Template('Hello {@who.name}');
        $view->who = ['name' => 'Dan'];
        self::assertTrue($view->render() == 'Hello Dan');

        $tmp       = new Template('Hello');
        $tmp->who  = 'World';
        $view      = new Template('Hello {@tmp.who}');
        $view->tmp = $tmp;
        self::assertTrue($view->render() == 'Hello World');

        try {
            $tmp        = new Template('Hello');
            $view       = new Template('Hello {@tmp.who}');
            $view->tmp  = $tmp;
            self::assertTrue($view->render() == 'Hello ');
        } catch (\Exception $e) {
            self::assertInstanceOf('\PHPUnit_Framework_Error_Notice', $e);
        }

        $view      = new Template('Hello {@who.toUpperCase}');
        $view->who = 'World';
        self::assertTrue($view->render() == 'Hello WORLD');

        $view      = new Template('Hello {@who.toLowerCase}');
        $view->who = 'World';
        self::assertTrue($view->render() == 'Hello world');
    }

    public function testUserViewIf()
    {
        $view = new Template('Hello{if who} World{/if}');
        $view->who = true;
        self::assertTrue($view->render() == 'Hello World');

        $view = new Template('Hello{if who} World{/if}');
        $view->who = false;
        self::assertTrue($view->render() == 'Hello');

        $view = new Template('Hello{ifnot who} World{/ifnot}');
        $view->who = true;
        self::assertTrue($view->render() == 'Hello');
    }

    public function testUserViewLoop()
    {
        $view = new Template('Hello {loop who}{@item}{/loop}');
        $view->who = ['W', 'o', 'r', 'l', 'd'];
        self::assertTrue($view->render() == 'Hello World');

        $view = new Template('Hello {loop who}{@item}{/loop}');
        self::assertTrue($view->render() == 'Hello ');

        $view = new Template('Hello {loop who}{@item}{/loop}');
        $view->who = 'World';
        self::assertTrue($view->render() == 'Hello World');
    }
}
