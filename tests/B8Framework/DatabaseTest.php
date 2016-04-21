<?php

namespace Tests\b8;

use b8\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
	protected $_host = 'localhost';
	protected $_user = 'b8_test';
	protected $_pass = 'b8_test';
	protected $_name = 'b8_test';

	public function testGetReadConnection()
	{
		Database::setDetails($this->_name, $this->_user, $this->_pass);
		Database::setReadServers([$this->_host]);

		$connection = Database::getConnection('read');

		$this->assertInstanceOf('\b8\Database', $connection);
	}

	public function testGetWriteConnection()
	{
		Database::setDetails($this->_name, $this->_user, $this->_pass);
		Database::setWriteServers([$this->_host]);

		$connection = Database::getConnection('write');

		$this->assertInstanceOf('\b8\Database', $connection);
	}

	public function testGetDetails()
	{
		Database::setDetails($this->_name, $this->_user, $this->_pass);
		Database::setReadServers(['localhost']);

		$details = Database::getConnection('read')->getDetails();
		$this->assertTrue(is_array($details));
		$this->assertTrue(($details['db'] == $this->_name));
		$this->assertTrue(($details['user'] == $this->_user));
		$this->assertTrue(($details['pass'] == $this->_pass));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testConnectionFailure()
	{
		Database::setDetails('non_existant', 'invalid_user', 'incorrect_password');
		Database::setReadServers(['localhost']);
		Database::getConnection('read');
	}
}