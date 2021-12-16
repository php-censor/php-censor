<?php

namespace PHPCensor\Store;

use PHPCensor\Store;

class Factory
{
    /**
     * @var Factory
     */
    protected static $instance;

    /**
     * A collection of the stores currently loaded by the factory.
     *
     * @var Store[]
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
     *
     * @return Store
     */
    public static function getStore($storeName)
    {
        $factory = self::getInstance();

        return $factory->loadStore($storeName);
    }

    protected function __construct()
    {
    }

    /**
     * @param string $store
     *
     * @return Store;
     */
    public function loadStore($store)
    {
        if (!isset($this->loadedStores[$store])) {
            $class = 'PHPCensor\\Store\\' . $store . 'Store';
            $obj   = new $class();

            $this->loadedStores[$store] = $obj;
        }

        return $this->loadedStores[$store];
    }
}
