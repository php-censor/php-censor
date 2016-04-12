<?php

namespace b8;

use b8\Config;
use b8\Http\Request;

if (!defined('B8_PATH')) {
	define('B8_PATH', dirname(__FILE__) . '/');
}

/**
* b8\Registry is now deprecated in favour of using the following classes:
* @see b8\Http\Request
* @see b8\Http\Response
* @see b8\Config
*/
class Registry
{
	/**
	 * @var \b8\Registry
	 */
	protected static $instance;
	protected $_data    = array();
	protected $_params  = null;

	/**
	* @var b8\Config
	*/
	protected $config;

	/**
	* @var b8\Http\Request
	*/
	protected $request;

	/**
	 * @return Registry
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	public function __construct(Config $config, Request $request)
	{
		$this->config = $config;
		$this->request = $request;

		self::$instance = $this;
	}

	public function get($key, $default = null)
	{
		return $this->config->get($key, $default);
	}

	public function set($key, $value)
	{
		return $this->config->set($key, $value);
	}

	public function setArray($array)
	{
		return $this->config->set($array);
	}

	public function getParams()
	{
		return $this->request->getParams();
	}

	public function getParam($key, $default)
	{
		return $this->request->getParam($key, $default);
	}

	public function setParam($key, $value)
	{
		return $this->request->setParam($key, $value);
	}

	public function unsetParam($key)
	{
		return $this->request->unsetParam($key);
	}

	public function parseInput()
	{
	}
}