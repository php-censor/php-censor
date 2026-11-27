<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\DatabaseConnection;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testConstructorWithNewPDO(): void
    {
        $databaseConnection = new DatabaseConnection('sqlite::memory');

        self::assertInstanceOf(DatabaseConnection::class, $databaseConnection);
        self::assertInstanceOf(\PDO::class, $databaseConnection->getPdo());
    }

    public function testConstructorWithExternalPDO(): void
    {
        $pdoConnection = $this->createStub(\PDO::class);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertInstanceOf(DatabaseConnection::class, $databaseConnection);
        self::assertInstanceOf(\PDO::class, $databaseConnection->getPdo());
    }

    public function testQuoteNamesForMySQL(): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::MYSQL_TYPE);

        $databaseConnection = new DatabaseConnection('mysql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            'SELECT `test_table`.`id`,`test`, `date` FROM `test_table` WHERE `condition` > 2',
            $databaseConnection->quoteNames(
                'SELECT {{test_table}}.{{id}},{{test}}, {{date}} FROM {{test_table}} WHERE {{condition}} > 2'
            )
        );
    }

    public function testQuoteNamesForPostgreSQL(): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $databaseConnection = new DatabaseConnection('pgsql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            'SELECT "test_table"."id","test", "date" FROM "test_table" WHERE "condition" > 2',
            $databaseConnection->quoteNames(
                'SELECT {{test_table}}.{{id}},{{test}}, {{date}} FROM {{test_table}} WHERE {{condition}} > 2'
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('prepareDataProvider')]
    public function testPrepare($result, array $option): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $pdoConnection
            ->method('prepare')
            ->with(
                'SELECT "test_table"."id","test", "date" FROM "test_table" WHERE "condition" > 2',
                $option
            )
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('pgsql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            $result,
            $databaseConnection->prepare(
                'SELECT {{test_table}}.{{id}},{{test}}, {{date}} FROM {{test_table}} WHERE {{condition}} > 2',
                $option
            )
        );
    }

    public function testLastInsertIdForPostgreSQL(): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $pdoConnection
            ->method('lastInsertId')
            ->with('"test_table_id_seq"')
            ->willReturn('10');

        $databaseConnection = new DatabaseConnection('pgsql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            10,
            $databaseConnection->lastInsertId('test_table')
        );
    }

    public function testLastInsertIdForPostgreSQLWithNonDefaultSequenceName(): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $pdoConnection
            ->method('lastInsertId')
            ->with('"sequence_test_table"')
            ->willReturn('10');

        $databaseConnection = new DatabaseConnection('pgsql', null, null, null, 'sequence_%s');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            10,
            $databaseConnection->lastInsertId('test_table')
        );
    }

    public function testLastInsertIdForMySQL(): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::MYSQL_TYPE);

        $pdoConnection
            ->method('lastInsertId')
            ->willReturn('11');

        $databaseConnection = new DatabaseConnection('mysql', null, null, null, 'sequence_%s');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            11,
            $databaseConnection->lastInsertId('test_table')
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('execDataProvider')]
    public function testExec($result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $pdoConnection
            ->method('exec')
            ->with('SELECT "test_table"."id","test", "date" FROM "test_table" WHERE "condition" > 2')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('pgsql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            $result,
            $databaseConnection->exec(
                'SELECT {{test_table}}.{{id}},{{test}}, {{date}} FROM {{test_table}} WHERE {{condition}} > 2'
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('queryDataProvider')]
    public function testQuery($result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn(DatabaseManager::POSTGRESQL_TYPE);

        $pdoConnection
            ->method('query')
            ->with('SELECT "test_table"."id","test", "date" FROM "test_table" WHERE "condition" > 2')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('pgsql');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals(
            $result,
            $databaseConnection->query(
                'SELECT {{test_table}}.{{id}},{{test}}, {{date}} FROM {{test_table}} WHERE {{condition}} > 2'
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanResultDataProvider')]
    public function testBeginTransaction(bool $result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('beginTransaction')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->beginTransaction());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanResultDataProvider')]
    public function testCommit(bool $result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('commit')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->commit());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanResultDataProvider')]
    public function testRollback(bool $result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('rollback')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->rollBack());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanResultDataProvider')]
    public function testInTransaction(bool $result): void
    {
        $pdoConnection = $this->createMock(\PDO::class);

        $pdoConnection
            ->method('inTransaction')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->inTransaction());
    }

    public static function prepareDataProvider(): array
    {
        return [
            [false, [\PDO::ATTR_CURSOR]],
            [false, []],
            [new \PDOStatement(), [\PDO::ATTR_CURSOR]],
            [new \PDOStatement(), []],
        ];
    }

    public static function execDataProvider(): array
    {
        return [
            [false],
            [0],
            [2],
        ];
    }

    public static function queryDataProvider(): array
    {
        return [
            [false],
            [new \PDOStatement()],
        ];
    }

    public static function booleanResultDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
