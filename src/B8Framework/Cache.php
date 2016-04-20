<?php

namespace b8;

/**
 * @package    b8
 * @subpackage Cache
 */

class Cache
{
    const TYPE_APC     = 'ApcCache';
    const TYPE_REQUEST = 'RequestCache';

    protected static $instance = [];

    /**
     * Get a cache object of a specified type.
     *
     * @param string $type
     *
     * @return mixed
     */
    public static function getCache($type = self::TYPE_REQUEST)
    {
        if (!isset(self::$instance[$type])) {
            $class = '\\b8\\Cache\\' . $type;
            self::$instance[$type] = new $class();
        }

        return self::$instance[$type];
    }
}
