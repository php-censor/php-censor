<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\DatabaseConnection;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testConstructorWithNewPDO()
    {
        $databaseConnection = new DatabaseConnection('sqlite::memory');

        self::assertInstanceOf(DatabaseConnection::class, $databaseConnection);
        self::assertInstanceOf(\PDO::class, $databaseConnection->getPdo());
    }

    public function testConstructorWithExternalPDO()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertInstanceOf(DatabaseConnection::class, $databaseConnection);
        self::assertInstanceOf(\PDO::class, $databaseConnection->getPdo());
    }

    public function testQuoteNamesForMySQL()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testQuoteNamesForPostgreSQL()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    /**
     * @dataProvider prepareDataProvider
     */
    public function testPrepare($result, array $option)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testLastInsertIdForPostgreSQL()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testLastInsertIdForPostgreSQLWithNonDefaultSequenceName()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testLastInsertIdForMySQL()
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    /**
     * @dataProvider execDataProvider
     */
    public function testExec($result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    /**
     * @dataProvider queryDataProvider
     */
    public function testQuery($result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

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

    /**
     * @dataProvider booleanResultDataProvider
     */
    public function testBeginTransaction(bool $result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdoConnection
            ->method('beginTransaction')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->beginTransaction());
    }

    /**
     * @dataProvider booleanResultDataProvider
     */
    public function testCommit(bool $result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdoConnection
            ->method('commit')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->commit());
    }

    /**
     * @dataProvider booleanResultDataProvider
     */
    public function testRollback(bool $result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdoConnection
            ->method('rollback')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->rollBack());
    }

    /**
     * @dataProvider booleanResultDataProvider
     */
    public function testInTransaction(bool $result)
    {
        $pdoConnection = $this
            ->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdoConnection
            ->method('inTransaction')
            ->willReturn($result);

        $databaseConnection = new DatabaseConnection('any');
        $databaseConnection->setPdo($pdoConnection);

        self::assertEquals($result, $databaseConnection->inTransaction());
    }

    public function prepareDataProvider(): array
    {
        return [
            [false, [\PDO::ATTR_CURSOR]],
            [false, []],
            [new \PDOStatement(), [\PDO::ATTR_CURSOR]],
            [new \PDOStatement(), []],
        ];
    }

    public function execDataProvider(): array
    {
        return [
            [false],
            [0],
            [2],
        ];
    }

    public function queryDataProvider(): array
    {
        return [
            [false],
            [new \PDOStatement()],
        ];
    }

    public function booleanResultDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
