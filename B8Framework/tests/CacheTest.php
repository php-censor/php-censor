<?php

require_once(dirname(__FILE__) . '/../b8/Registry.php');
require_once(dirname(__FILE__) . '/../b8/Cache.php');

use b8\Registry,
	b8\Cache;

class CacheTest extends PHPUnit_Framework_TestCase
{
	public function testCreateSingleton()
	{
		$cache = b8\Cache::getInstance();
		$this->assertTrue($cache instanceof Cache);
	}

	public function testDisableCaching()
	{
		b8\Registry::getInstance()->set('DisableCaching', true);

		$cache = b8\Cache::getInstance();
		$this->assertFalse($cache->isEnabled());
		$this->assertFalse($cache->set('anything', 10));
		$this->assertTrue(is_null($cache->get('anything')));

		b8\Registry::getInstance()->set('DisableCaching', false);
	}

	public function testCaching()
	{
		$cache = b8\Cache::getInstance();

		if($cache->isEnabled())
		{
			$this->assertTrue($cache->set('anything', 10));
			$this->assertTrue($cache->get('anything') == 10);
			$this->assertTrue(is_null($cache->get('invalid')));
		}
	}
}
