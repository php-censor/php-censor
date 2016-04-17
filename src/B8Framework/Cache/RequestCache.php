<?php

namespace b8\Cache;

use b8\Type;

class RequestCache implements Type\Cache
{
	protected $data = array();

	/**
	 * Check if caching is enabled.
	 */
	public function isEnabled()
	{
		return true;
	}

	/**
	 * Get item from the cache:
	 */
	public function get($key, $default = null)
	{
		return $this->contains($key) ? $this->data[$key] : $default;
	}

	/**
	 * Add an item to the cache:
	 */
	public function set($key, $value = null, $ttl = 0)
	{
		$this->data[$key] = $value;

		return $this;
	}

	/**
	 * Remove an item from the cache:
	 */
	public function delete($key)
	{
		if ($this->contains($key)) {
			unset($this->data[$key]);
		}
		
		return $this;
	}

	/**
	 * Check if an item is in the cache:
	 */
	public function contains($key)
	{
		return array_key_exists($key, $this->data);
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