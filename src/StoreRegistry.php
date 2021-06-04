<?php

declare(strict_types = 1);

namespace PHPCensor;

use PHPCensor\Common\Exception\RuntimeException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
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

    /**
     * @param string $storeName
     *
     * @return Store|null
     *
     * @throws RuntimeException
     */
    public function get(string $storeName): ?Store
    {
        if (!isset($this->loadedStores[$storeName])) {
            try {
                $class = 'PHPCensor\\Store\\' . $storeName . 'Store';
                $store = new $class($this->databaseManager, $this);

                $this->loadedStores[$storeName] = $store;
            } catch (\Throwable $exception) {
                throw new RuntimeException($exception->getMessage());
            }
        }

        return $this->loadedStores[$storeName];
    }
}
