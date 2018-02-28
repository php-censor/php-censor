<?php

namespace Tests\b8;

use b8\Config;
use b8\Database;
use PHPUnit\Framework\TestCase;

class DatabasePostgresqlTest extends TestCase
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
                    'type'     => Database::POSTGRESQL_TYPE,
                    'name'     => POSTGRESQL_DBNAME,
                    'username' => POSTGRESQL_USER,
                    'password' => POSTGRESQL_PASSWORD,
                ],
            ],
        ]);
        Database::reset();
    }

    protected function checkDatabaseConnection()
    {
        if (!extension_loaded('pgsql')) {
            $this->markTestSkipped('Test skipped because Pgsql extension doesn`t exist.');
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
        self::assertEquals(POSTGRESQL_DBNAME, $writeDetails['db']);
        self::assertEquals(POSTGRESQL_USER, $writeDetails['user']);
        self::assertEquals(POSTGRESQL_PASSWORD, $writeDetails['pass']);

        $readDetails  = Database::getConnection('read')->getDetails();

        self::assertTrue(is_array($readDetails));
        self::assertEquals(POSTGRESQL_DBNAME, $readDetails['db']);
        self::assertEquals(POSTGRESQL_USER, $readDetails['user']);
        self::assertEquals(POSTGRESQL_PASSWORD, $readDetails['pass']);
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
                    'type'     => Database::POSTGRESQL_TYPE,
                    'name'     => 'b8_test_2',
                    'username' => '',
                    'password' => '',
                ],
            ],
        ]);

        Database::getConnection('read');
    }
}
