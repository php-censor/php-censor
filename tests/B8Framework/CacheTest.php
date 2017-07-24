<?php

namespace Tests\b8;

use b8\Config, b8\Cache;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateSingleton()
    {
        $cache = Cache::getCache(Cache::TYPE_APC);
        self::assertInstanceOf('\b8\Cache\ApcCache', $cache);
    }

    public function testDisableCaching()
    {
        $config = new Config();
        Config::getInstance()->set('DisableCaching', true);

        $cache = Cache::getCache(Cache::TYPE_APC);
        $this->assertFalse($cache->isEnabled());
        $this->assertFalse($cache->set('anything', 10));
        $this->assertTrue(is_null($cache->get('anything')));

        Config::getInstance()->set('DisableCaching', false);
    }

    public function testCaching()
    {
        $cache = Cache::getCache(Cache::TYPE_APC);

        if ($cache->isEnabled()) {
            $this->assertTrue($cache->set('anything', 10));
            $this->assertTrue($cache->get('anything') == 10);
            $this->assertTrue(is_null($cache->get('invalid')));
        }
    }
}
