<?php

namespace b8\Store;

use b8\Config;

class Factory
{
    /**
     * @var \b8\Store\Factory
     */
    protected static $instance;

    /**
     * A collection of the stores currently loaded by the factory.
     *
     * @var \b8\Store[]
     */
    protected $loadedStores = [];

    /**
     * @return Factory
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $storeName Store name (should match a model name).
     * @param string $namespace
     *
     * @return \b8\Store
     */
    public static function getStore($storeName, $namespace = null)
    {
        $factory = self::getInstance();
        return $factory->loadStore($storeName, $namespace);
    }

    protected function __construct()
    {
    }

    /**
     * @param string $store
     * @param string $namespace
     *
     * @return \b8\Store;
     */
    public function loadStore($store, $namespace = null)
    {
        if (!isset($this->loadedStores[$store])) {
            $namespace = is_null($namespace)
                ? Config::getInstance()->get('b8.app.namespace')
                : $namespace;

            $class                      = $namespace . '\\Store\\' . $store . 'Store';
            $obj                        = new $class();
            $this->loadedStores[$store] = $obj;
        }

        return $this->loadedStores[$store];
    }
}
