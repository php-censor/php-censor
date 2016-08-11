<?php

namespace Tests\b8;

use b8\Config;
use b8\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $config = new Config([
            'b8' => [
                'database' => [
                    'servers' => [
                        'read'  => 'localhost',
                        'write' => 'localhost',
                    ],
                    'name'     => 'b8_test',
                    'username' => 'root',
                    'password' => 'root',
                ],
            ],
        ]);
    }
    
    public function testGetReadConnection()
    {
        $connection = Database::getConnection('read');

        $this->assertInstanceOf('\b8\Database', $connection);
    }

    public function testGetWriteConnection()
    {
        $connection = Database::getConnection('write');

        $this->assertInstanceOf('\b8\Database', $connection);
    }

    public function testGetDetails()
    {
        $details = Database::getConnection('read')->getDetails();
        $this->assertTrue(is_array($details));
        $this->assertTrue(($details['db'] == 'b8_test'));
        $this->assertTrue(($details['user'] == 'root'));
        $this->assertTrue(($details['pass'] == 'root'));
    }

    /**
     * @expectedException \Exception
     */
    public function testConnectionFailure()
    {
        Database::reset();

        $config = new Config([
            'b8' => [
                'database' => [
                    'servers' => [
                        'read'  => 'localhost',
                        'write' => 'localhost',
                    ],
                    'name'     => 'b8_test_2',
                    'username' => '',
                    'password' => '',
                ],
            ],
        ]);

        Database::getConnection('read');
    }
}