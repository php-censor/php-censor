<?php

declare(strict_types = 1);

namespace PHPCensor;

class StoreRegistry
{
    private DatabaseManager $databaseManager;

    /**
     * A collection of the stores currently loaded by the factory.
     *
     * @var Store[]
     */
    private array $loadedStores = [];

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function get(string $storeName): ?Store
    {
        if (!isset($this->loadedStores[$storeName])) {
            $class = 'PHPCensor\\Store\\' . $storeName . 'Store';
            $store = new $class($this->databaseManager, $this);

            $this->loadedStores[$storeName] = $store;
        }

        return $this->loadedStores[$storeName];
    }
}
