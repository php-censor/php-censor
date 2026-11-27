<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PDO;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\TestCase;

class DatabaseManagerTest extends TestCase
{
    public function testMysqlInitCommandAttribute(): void
    {
        $expected = \PHP_VERSION_ID >= 80400
            ? \Pdo\Mysql::ATTR_INIT_COMMAND
            : PDO::MYSQL_ATTR_INIT_COMMAND;

        self::assertSame($expected, DatabaseManager::mysqlInitCommandAttribute());
    }
}
