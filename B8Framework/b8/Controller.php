<?php

namespace b8;

use b8\Config;
use b8\Http\Request;
use b8\Http\Response;
use b8\View;

/**
 * b8 Abstract Controller class
 * @package b8
 */
abstract class Controller
{
	/**
	* @var b8\Http\Request
	*/
	protected $request;

	/**
	* @var b8\Http\Response
	*/
	protected $response;

	/**
	* @var b8\Config
	*/
	protected $config;

	/**
	* @var b8\View
	*/
	protected $controllerView;

	/**
	* @var b8\View
	*/
	protected $view;

	public function __construct(Config $config, Request $request, Response $response)
	{
		$this->config = $config;
		$this->request = $request;
		$this->response = $response;
	}

	public function hasAction($name)
	{
		if (method_exists($this, $name)) {
			return true;
		}

		if (method_exists($this, '__call')) {
			return true;
		}

		return false;
	}

	/**
	* Handles an action on this controller and returns a Response object.
	* @return b8\Http\Response
	*/
	public function handleAction($action, $actionParams)
	{
		return call_user_func_array(array($this, $action), $actionParams);		
	}

	/**
	 * Initialise the controller.
	 */
	abstract public function init();

	/**
	 * Get a hash of incoming request parameters ($_GET, $_POST)
	 *
	 * @return array
	 */
	public function getParams()
	{
	    return $this->request->getParams();
	}

	/**
	 * Get a specific incoming request parameter.
	 *
	 * @param      $key
	 * @param mixed $default    Default return value (if key does not exist)
	 *
	 * @return mixed
	 */
	public function getParam($key, $default = null)
	{
	    return $this->request->getParam($key, $default);
	}

	/**
	 * Change the value of an incoming request parameter.
	 * @param $key
	 * @param $value
	 */
	public function setParam($key, $value)
	{
		return $this->request->setParam($key, $value);
	}

	/**
	 * Remove an incoming request parameter.
	 * @param $key
	 */
	public function unsetParam($key)
	{
		return $this->request->unsetParam($key);
	}
}