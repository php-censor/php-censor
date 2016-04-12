<?php

namespace b8\Cache;

use b8\Type;

class ApcCache implements Type\Cache
{
	/**
	 * Check if caching is enabled.
	 */
	public function isEnabled()
	{
		$rtn = false;

		$apcCli = ini_get('apc.enable_cli');

		if( function_exists('apc_fetch') &&
			(php_sapi_name() != 'cli' || in_array($apcCli, array('1', 1, true, 'On'))))
		{
			$rtn = true;
		}

		return $rtn;
	}

	/**
	 * Get item from the cache:
	 */
	public function get($key, $default = null)
	{
		if (!$this->isEnabled()) {
			return $default;
		}

		$success = false;
		$rtn     = apc_fetch($key, $success);

		if (!$success) {
			$rtn = $default;
		}

		return $rtn;
	}

	/**
	 * Add an item to the cache:
	 */
	public function set($key, $value = null, $ttl = 0)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		return apc_store($key, $value, $ttl);
	}

	/**
	 * Remove an item from the cache:
	 */
	public function delete($key)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		return apc_delete($key);
	}

	/**
	 * Check if an item is in the cache:
	 */
	public function contains($key)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		return apc_exists($key);
	}

	/**
    * Short-hand syntax for get()
    * @see Config::get()
    */
    public function __get($key)
    {
        return $this->get($key, null);
    }

    /**
    * Short-hand syntax for set()
    * @see Config::set()
    */
    public function __set($key, $value = null)
    {
        return $this->set($key, $value);
    }

    /**
    * Is set
    */
    public function __isset($key)
    {
        return $this->contains($key);
    }

    /**
    * Unset
    */
    public function __unset($key)
    {
        $this->delete($key);
    }
}