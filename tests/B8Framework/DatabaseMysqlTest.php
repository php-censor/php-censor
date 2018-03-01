<?php

namespace Tests\b8;

use b8\Config;
use b8\Database;

class DatabaseMysqlTest extends \PHPUnit_Extensions_Database_TestCase
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

        if (extension_loaded('mysqli')) {
            if (null === $this->connection) {
                try {
                    $pdo = new \PDO(
                        'mysql:host=localhost;dbname=' . MYSQL_DBNAME,
                        MYSQL_USER,
                        MYSQL_PASSWORD
                    );

                    $this->connection = $this->createDefaultDBConnection($pdo, MYSQL_DBNAME);

                    $this->connection->getConnection()->query('
                        CREATE TABLE IF NOT EXISTS `databaseMysqlTest` (
                            `id`         int(11) NOT NULL AUTO_INCREMENT,
                            `projectId`  int(11) NOT NULL,
                            `branch`     varchar(250) NOT NULL DEFAULT \'master\',
                            `createDate` datetime,
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
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
            $this->markTestSkipped('Test skipped because MySQL database/user/extension doesn`t exist.');
        }

        return $this->connection;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'databaseMysqlTest' => [[
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
                    'type'     => Database::MYSQL_TYPE,
                    'name'     => MYSQL_DBNAME,
                    'username' => MYSQL_USER,
                    'password' => MYSQL_PASSWORD,
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
        self::assertEquals(MYSQL_DBNAME, $writeDetails['db']);
        self::assertEquals(MYSQL_USER, $writeDetails['user']);
        self::assertEquals(MYSQL_PASSWORD, $writeDetails['pass']);

        $readDetails  = $readConnection->getDetails();

        self::assertTrue(is_array($readDetails));
        self::assertEquals(MYSQL_DBNAME, $readDetails['db']);
        self::assertEquals(MYSQL_USER, $readDetails['user']);
        self::assertEquals(MYSQL_PASSWORD, $readDetails['pass']);

        self::assertEquals('mysql:host=localhost;dbname=b8_test', $readConnection->getDsn());
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
                                'port' => 3306,
                            ],
                        ],
                        'write' => [
                            [
                                'host' => 'localhost',
                                'port' => 3306,
                            ],
                        ],
                    ],
                    'type'     => Database::MYSQL_TYPE,
                    'name'     => MYSQL_DBNAME,
                    'username' => MYSQL_USER,
                    'password' => MYSQL_PASSWORD,
                ],
            ],
        ]);
        Database::reset();

        $writeConnection = Database::getConnection('write');
        $readConnection  = Database::getConnection('read');

        self::assertInstanceOf('\b8\Database', $writeConnection);
        self::assertInstanceOf('\b8\Database', $readConnection);

        self::assertEquals('mysql:host=localhost;port=3306;dbname=b8_test', $readConnection->getDsn());
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
                    'type'     => Database::MYSQL_TYPE,
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

        $sql   = 'SELECT * FROM {{databaseMysqlTest}} WHERE {{projectId}} = :projectId';
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
        $writeConnection = Database::getConnection('write');

        $sql   = 'INSERT INTO {{databaseMysqlTest}} ({{projectId}}) VALUES (3)';
        $query = $writeConnection->prepareCommon($sql);

        $query->execute();

        self::assertEquals(4, $writeConnection->lastInsertIdExtended());
    }
}
