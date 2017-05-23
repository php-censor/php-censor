<?php

namespace PHPCensor\Logging;

use Monolog\Logger;

/**
 * Class LoggerConfig
 */
class LoggerConfig
{
    const KEY_ALWAYS_LOADED = "_";
    private $config;
    private $cache = [];

    /**
     * Each key of the array is the name of a logger. The value of
     * each key should be an array or a function that returns an
     * array of LogHandlers.
     * @param array $configArray
     */
    public function __construct(array $configArray = [])
    {
        $this->config = $configArray;
    }

    /**
     * Returns an instance of Monolog with all configured handlers
     * added. The Monolog instance will be given $name.
     * @param $name
     * @return Logger
     */
    public function getFor($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $handlers = $this->getHandlers(self::KEY_ALWAYS_LOADED);
        if ($name !== self::KEY_ALWAYS_LOADED) {
            $handlers = array_merge($handlers, $this->getHandlers($name));
        }

        $logger = new Logger($name, $handlers);
        Handler::register($logger);
        $this->cache[$name] = $logger;

        return $logger;
    }

    /**
     * Return an array of enabled log handlers.
     * @param $key
     * @return array|mixed
     */
    protected function getHandlers($key)
    {
        $handlers = [];

        // They key is expected to either be an array or
        // a callable function that returns an array
        if (isset($this->config[$key])) {
            if (is_callable($this->config[$key])) {
                $handlers = call_user_func($this->config[$key]);
            } elseif (is_array($this->config[$key])) {
                $handlers = $this->config[$key];
            }
        }
        return $handlers;
    }
}
