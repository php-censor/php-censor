<?php

namespace Tests\PHPCensor;

use PHPCensor\View;

class ViewTest extends \PHPUnit\Framework\TestCase
{
    public function testSimpleView()
    {
        $view = new View('simple', ROOT_DIR . 'tests/data/View/');
        self::assertTrue($view->render() == 'Hello');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidView()
    {
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
