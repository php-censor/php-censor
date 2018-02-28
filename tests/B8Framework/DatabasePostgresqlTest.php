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
                    'type'     => 'pgsql',
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

    public function testGetWriteConnection()
    {
        $this->checkDatabaseConnection();

        $connection = Database::getConnection('write');
        self::assertInstanceOf('\b8\Database', $connection);
    }

    public function testGetDetails()
    {
        $this->checkDatabaseConnection();

        $details = Database::getConnection('read')->getDetails();
        self::assertTrue(is_array($details));
        self::assertTrue(($details['db'] === POSTGRESQL_DBNAME));
        self::assertTrue(($details['user'] === POSTGRESQL_USER));
        self::assertTrue(($details['pass'] === POSTGRESQL_PASSWORD));
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
                    'type'     => 'pgsql',
                    'name'     => 'b8_test_2',
                    'username' => '',
                    'password' => '',
                ],
            ],
        ]);

        Database::getConnection('read');
    }
}
