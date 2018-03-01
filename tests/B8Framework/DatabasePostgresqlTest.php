<?php

namespace Tests\b8;

use b8\Config;
use b8\Database;

class DatabasePostgresqlTest extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection|null
     */
    protected $connection = null;

    /**
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (extension_loaded('pgsql')) {
            if (null === $this->connection) {
                try {
                    $pdo = new \PDO(
                        'pgsql:host=localhost;dbname=' . POSTGRESQL_DBNAME,
                        POSTGRESQL_USER,
                        POSTGRESQL_PASSWORD
                    );

                    $this->connection = $this->createDefaultDBConnection($pdo, POSTGRESQL_DBNAME);

                    $this->connection->getConnection()->query('
                        CREATE TABLE IF NOT EXISTS "databasePostgresqlTest" (
                            "id"         SERIAL,
                            "projectId"  integer NOT NULL,
                            "branch"     character varying(250) NOT NULL DEFAULT \'master\',
                            "createDate" timestamp without time zone,
                            PRIMARY KEY ("id")
                        )
                    ');
                } catch (\PDOException $ex) {
                    $this->connection = null;
                }
            }
        } else {
            $this->connection = null;
        }
    }

    /**
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if (null === $this->connection) {
            $this->markTestSkipped('Test skipped because PostgreSQL database/user/extension doesn`t exist.');
        }

        return $this->connection;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'databasePostgresqlTest' => [[
                'id'         => 1,
                'projectId'  => 1,
                'branch'     => 'master',
                'createDate' => null,
            ], [
                'id'         => 2,
                'projectId'  => 2,
                'branch'     => 'dev',
                'createDate' => '2018-02-20 01:01:01',
            ], [
                'id'         => 3,
                'projectId'  => 2,
                'branch'     => 'master',
                'createDate' => '2018-02-21 02:02:02',
            ]],
        ]);
    }

    protected function setUp()
    {
        parent::setUp();

        new Config([
            'b8' => [
                'database' => [
                    'servers' => [
                        'read'  => [
                            ['host' => 'localhost'],
                        ],
                        'write' => [
                            ['host' => 'localhost'],
                        ],
                    ],
                    'type'     => Database::POSTGRESQL_TYPE,
                    'name'     => POSTGRESQL_DBNAME,
                    'username' => POSTGRESQL_USER,
                    'password' => POSTGRESQL_PASSWORD,
                ],
            ],
        ]);
        Database::reset();
    }

    public function testGetConnection()
    {
        $writeConnection = Database::getConnection('write');
        $readConnection  = Database::getConnection('read');

        self::assertInstanceOf('\b8\Database', $writeConnection);
        self::assertInstanceOf('\b8\Database', $readConnection);

        $writeDetails = $writeConnection->getDetails();

        self::assertTrue(is_array($writeDetails));
        self::assertEquals(POSTGRESQL_DBNAME, $writeDetails['db']);
        self::assertEquals(POSTGRESQL_USER, $writeDetails['user']);
        self::assertEquals(POSTGRESQL_PASSWORD, $writeDetails['pass']);

        $readDetails  = $readConnection->getDetails();

        self::assertTrue(is_array($readDetails));
        self::assertEquals(POSTGRESQL_DBNAME, $readDetails['db']);
        self::assertEquals(POSTGRESQL_USER, $readDetails['user']);
        self::assertEquals(POSTGRESQL_PASSWORD, $readDetails['pass']);

        self::assertEquals('pgsql:host=localhost;dbname=b8_test', $readConnection->getDsn());
    }

    public function testGetWriteConnectionWithPort()
    {
        new Config([
            'b8' => [
                'database' => [
                    'servers' => [
                        'read'  => [
                            [
                                'host' => 'localhost',
                                'port' => 5432,
                            ],
                        ],
                        'write' => [
                            [
                                'host' => 'localhost',
                                'port' => 5432,
                            ],
                        ],
                    ],
                    'type'     => Database::POSTGRESQL_TYPE,
                    'name'     => POSTGRESQL_DBNAME,
                    'username' => POSTGRESQL_USER,
                    'password' => POSTGRESQL_PASSWORD,
                ],
            ],
        ]);
        Database::reset();

        $writeConnection = Database::getConnection('write');
        $readConnection  = Database::getConnection('read');

        self::assertInstanceOf('\b8\Database', $writeConnection);
        self::assertInstanceOf('\b8\Database', $readConnection);

        self::assertEquals('pgsql:host=localhost;port=5432;dbname=b8_test', $readConnection->getDsn());
    }

    /**
     * @expectedException \Exception
     */
    public function testConnectionFailure()
    {
        new Config([
            'b8' => [
                'database' => [
                    'servers' => [
                        'read'  => [
                            ['host' => 'localhost'],
                        ],
                        'write' => [
                            ['host' => 'localhost'],
                        ],
                    ],
                    'type'     => Database::POSTGRESQL_TYPE,
                    'name'     => 'b8_test_2',
                    'username' => '',
                    'password' => '',
                ],
            ],
        ]);
        Database::reset();

        Database::getConnection('read');
    }

    public function testPrepareCommon()
    {
        $readConnection = Database::getConnection('read');

        $sql   = 'SELECT * FROM {{databasePostgresqlTest}} WHERE {{projectId}} = :projectId';
        $query = $readConnection->prepareCommon($sql);

        $query->bindValue(':projectId', 1);
        $query->execute();

        $data = $query->fetchAll(\PDO::FETCH_ASSOC);

        self::assertEquals(1, count($data));
        self::assertEquals([[
            'id'         => 1,
            'projectId'  => 1,
            'branch'     => 'master',
            'createDate' => null,
        ]], $data);
    }

    public function testLastInsertIdExtended()
    {
        $this->connection->getConnection()->query('
            ALTER SEQUENCE "databasePostgresqlTest_id_seq" RESTART WITH 4;
        ');

        $writeConnection = Database::getConnection('write');

        $sql   = 'INSERT INTO {{databasePostgresqlTest}} ({{projectId}}) VALUES (3)';
        $query = $writeConnection->prepareCommon($sql);

        $query->execute();

        self::assertEquals(4, $writeConnection->lastInsertIdExtended('databasePostgresqlTest'));
    }
}
