<?php

require_once(dirname(__FILE__) . '/../b8/Registry.php');
require_once(dirname(__FILE__) . '/../b8/Database.php');

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
	protected $_host = 'localhost';
	protected $_user = 'b8_test';
	protected $_pass = 'b8_test';
	protected $_name = 'b8_test';

	public function testGetReadConnection()
	{
		\b8\Database::setDetails($this->_name, $this->_user, $this->_pass);
		\b8\Database::setReadServers(array($this->_host));

		$connection = \b8\Database::getConnection('read');

		$this->assertInstanceOf('\b8\Database', $connection);
	}

	public function testGetWriteConnection()
	{
		\b8\Database::setDetails($this->_name, $this->_user, $this->_pass);
		\b8\Database::setWriteServers(array($this->_host));

		$connection = \b8\Database::getConnection('write');

		$this->assertInstanceOf('\b8\Database', $connection);
	}

	public function testGetDetails()
	{
		\b8\Database::setDetails($this->_name, $this->_user, $this->_pass);
		\b8\Database::setReadServers(array('localhost'));

		$details = \b8\Database::getConnection('read')->getDetails();
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
		\b8\Database::setDetails('non_existant', 'invalid_user', 'incorrect_password');
		\b8\Database::setReadServers(array('localhost'));
		\b8\Database::getConnection('read');
	}
}