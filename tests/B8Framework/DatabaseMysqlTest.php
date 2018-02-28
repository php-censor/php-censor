<?php

namespace Tests\b8;

use b8\Config;
use b8\Database;
use PHPUnit\Framework\TestCase;

class DatabaseMysqlTest extends TestCase
{
    protected function setUp()
    {
        $config = new Config([
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

    protected function checkDatabaseConnection()
    {
        if (!extension_loaded('mysqli')) {
            $this->markTestSkipped('Test skipped because Mysqli extension doesn`t exist.');
        }

        try {
            $connection = Database::getConnection('read');
        } catch (\Exception $e) {
            if ('Could not connect to any read servers.' === $e->getMessage()) {
                $this->markTestSkipped('Test skipped because test database doesn`t exist.');
            } else {
                throw $e;
            }
        }
    }

    public function testGetConnection()
    {
        $this->checkDatabaseConnection();

        $writeConnection = Database::getConnection('write');
        $readConnection  = Database::getConnection('read');

        self::assertInstanceOf('\b8\Database', $writeConnection);
        self::assertInstanceOf('\b8\Database', $readConnection);

        $writeDetails = Database::getConnection('write')->getDetails();

        self::assertTrue(is_array($writeDetails));
        self::assertEquals(MYSQL_DBNAME, $writeDetails['db']);
        self::assertEquals(MYSQL_USER, $writeDetails['user']);
        self::assertEquals(MYSQL_PASSWORD, $writeDetails['pass']);

        $readDetails  = Database::getConnection('read')->getDetails();

        self::assertTrue(is_array($readDetails));
        self::assertEquals(MYSQL_DBNAME, $readDetails['db']);
        self::assertEquals(MYSQL_USER, $readDetails['user']);
        self::assertEquals(MYSQL_PASSWORD, $readDetails['pass']);
    }

    public function testGetWriteConnectionWithPort()
    {
        $config = new Config([
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

        $this->checkDatabaseConnection();

        $writeConnection = Database::getConnection('write');
        $readConnection  = Database::getConnection('read');

        self::assertInstanceOf('\b8\Database', $writeConnection);
        self::assertInstanceOf('\b8\Database', $readConnection);
    }

    /**
     * @expectedException \Exception
     */
    public function testConnectionFailure()
    {
        $this->checkDatabaseConnection();

        Database::reset();

        $config = new Config([
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

        Database::getConnection('read');
    }
}
