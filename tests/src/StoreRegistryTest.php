<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\DatabaseManager;
use PHPCensor\Store\BuildStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

class StoreRegistryTest extends TestCase
{
    public function testConstructor()
    {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeRegistry = new StoreRegistry($databaseManager);

        self::assertInstanceOf(StoreRegistry::class, $storeRegistry);
    }

    public function testGetSuccess()
    {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeRegistry = new StoreRegistry($databaseManager);
        $buildStore    = $storeRegistry->get('Build');

        self::assertInstanceOf(BuildStore::class, $buildStore);
    }

    public function testGetSuccessFromCache()
    {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeRegistry = new StoreRegistry($databaseManager);
        $buildStore    = $storeRegistry->get('Build');
        $buildStore2   = $storeRegistry->get('Build');

        self::assertInstanceOf(BuildStore::class, $buildStore2);
        self::assertSame($buildStore, $buildStore2);
    }

    public function testGetNotFound()
    {
        self::expectException(RuntimeException::class);

        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeRegistry = new StoreRegistry($databaseManager);
        $buildStore    = $storeRegistry->get('NotExists');
    }
}
