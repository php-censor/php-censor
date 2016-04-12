<?php

require_once(dirname(__FILE__) . '/../b8/Registry.php');
require_once(dirname(__FILE__) . '/../b8/View.php');
require_once(dirname(__FILE__) . '/../b8/View/UserView.php');
require_once(dirname(__FILE__) . '/../b8/View/Helper/Format.php');
require_once(dirname(__FILE__) . '/../b8/Exception/HttpException.php');

use b8\View,
	b8\View\UserView;

class ViewTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpleView()
	{
		$view = new b8\View('simple', dirname(__FILE__) . '/data/view/');
		$this->assertTrue($view->render() == 'Hello');
	}

	/**
	 * @expectedException \Exception
	 */
	public function testInvalidView()
	{
		new b8\View('dogs', dirname(__FILE__) . '/data/view/');
	}

	public function testViewVars()
	{
		$view = new b8\View('vars', dirname(__FILE__) . '/data/view/');
		$view->who = 'World';

		$this->assertTrue(isset($view->who));
		$this->assertFalse(isset($view->what));
		$this->assertTrue($view->render() == 'Hello World');
	}

	public function testFormatViewHelper()
	{
		$view = new b8\View('format', dirname(__FILE__) . '/data/view/');
		$view->number = 1000000.25;
		$view->symbol = true;

		$this->assertTrue($view->render() == '£1,000,000.25');

		$view->number = 1024;
		$view->symbol = false;
		$this->assertTrue($view->render() == '1,024.00');
	}

	/**
	 * @expectedException \b8\Exception\HttpException
	 */
	public function testInvalidHelper()
	{
		$view = new UserView('{@Invalid:test}');
		$view->render();
	}

	public function testSimpleUserView()
	{
		$view = new UserView('Hello');
		$this->assertTrue($view->render() == 'Hello');
	}

	public function testUserViewYear()
	{
		$view = new UserView('{@year}');
		$this->assertTrue($view->render() == date('Y'));
	}

	public function testUserViewVars()
	{
		$view = new UserView('Hello {@who}');
		$view->who = 'World';
		$this->assertTrue($view->render() == 'Hello World');

		$view = new UserView('Hello {@who}');
		$this->assertTrue($view->render() == 'Hello ');

		$view = new UserView('Hello {@who.name}');
		$view->who = array('name' => 'Dan');
		$this->assertTrue($view->render() == 'Hello Dan');

		$tmp = new UserView('Hello');
		$tmp->who = 'World';
		$view = new UserView('Hello {@tmp.who}');
		$view->tmp = $tmp;

		$this->assertTrue($view->render() == 'Hello World');

		$tmp    = new UserView('Hello');
		$view   = new UserView('Hello {@tmp.who}');
		$view->tmp = $tmp;

		$this->assertTrue($view->render() == 'Hello ');

		$view = new UserView('Hello {@who.toUpperCase}');
		$view->who = 'World';
		$this->assertTrue($view->render() == 'Hello WORLD');

		$view = new UserView('Hello {@who.toLowerCase}');
		$view->who = 'World';
		$this->assertTrue($view->render() == 'Hello world');
	}

	public function testUserViewIf()
	{
		$view = new UserView('Hello{if who} World{/if}');
		$view->who = true;
		$this->assertTrue($view->render() == 'Hello World');

		$view = new UserView('Hello{if who} World{/if}');
		$view->who = false;
		$this->assertTrue($view->render() == 'Hello');

		$view = new UserView('Hello{ifnot who} World{/ifnot}');
		$view->who = true;
		$this->assertTrue($view->render() == 'Hello');

		$view = new UserView('Hello {if Format:not_present}World{/if}');
		$this->assertTrue($view->render() == 'Hello ');

		$view = new UserView('Hello {ifnot Format:not_present}World{/ifnot}');
		$this->assertTrue($view->render() == 'Hello World');
	}

	public function testUserViewLoop()
	{
		$view = new UserView('Hello {loop who}{@item}{/loop}');
		$view->who = array('W', 'o', 'r', 'l', 'd');
		$this->assertTrue($view->render() == 'Hello World');

		$view = new UserView('Hello {loop who}{@item}{/loop}');
		$this->assertTrue($view->render() == 'Hello ');

		$view = new UserView('Hello {loop who}{@item}{/loop}');
		$view->who = 'World';
		$this->assertTrue($view->render() == 'Hello World');
	}
}