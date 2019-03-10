<?php

namespace PHPCensor;

use Symfony\Component\Yaml\Parser as YamlParser;

class Config
{
    /**
     * @var Config
     */
    protected static $instance;

    /**
     * @return Config
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $settings
     */
    public function __construct($settings = null)
    {
        self::$instance = $this;

        if (empty($settings)) {
            return;
        } elseif (is_array($settings)) {
            // Array of setting data.
            $this->setArray($settings);
        } elseif (is_string($settings) && file_exists($settings)) {
            $this->loadYaml($settings);
        }
    }

    /**
     * @param string $yamlFile
     */
    public function loadYaml($yamlFile)
    {
        // Path to a YAML file.
        $parser = new YamlParser();
        $yaml   = file_get_contents($yamlFile);
        $config = (array)$parser->parse($yaml);

        if (empty($config)) {
            return;
        }

        $this->setArray($config);
    }

    /**
     * Get a configuration value by key, returning a default value if not set.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $keyParts = explode('.', $key);
        $selected = $this->config;

        $i        = -1;
        $lastPart = count($keyParts) - 1;
        while ($part = array_shift($keyParts)) {
            $i++;

            if (!array_key_exists($part, $selected)) {
                return $default;
            }

            if ($i === $lastPart) {
                return $selected[$part];
            } else {
                $selected = $selected[$part];
            }
        }

        return $default;
    }

    /**
     * Set a value by key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($key, $value = null)
    {
        $this->config[$key] = $value;

        return true;
    }

    /**
     * Set an array of values.
     *
     * @param $array
     */
    public function setArray($array)
    {
        self::deepMerge($this->config, $array);
    }

    /**
     * Short-hand syntax for get()
     * @see Config::get()
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Short-hand syntax for set()
     * @see Config::set()
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function __set($key, $value = null)
    {
        return $this->set($key, $value);
    }

    /**
     * Is set
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->config[$key]);
    }

    /**
     * Unset
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->config[$key]);
    }

    /**
     * Deeply merge the $target array onto the $source array. The $source array will be modified!
     *
     * @param array $source
     * @param array $target
     */
    public static function deepMerge(&$source, $target)
    {
        if (count($source) === 0) {
            $source = $target;
            return;
        }

        foreach ($target as $targetKey => $targetValue) {
            if (isset($source[$targetKey])) {
                if (!is_array($source[$targetKey]) && !is_array($targetValue)) {
                    // Neither value is an array, overwrite
                    $source[$targetKey] = $targetValue;
                } elseif (is_array($source[$targetKey]) && is_array($targetValue)) {
                    // Both are arrays, deep merge them
                    self::deepMerge($source[$targetKey], $targetValue);
                } elseif (is_array($source[$targetKey])) {
                    // Source is the array, push target value
                    $source[$targetKey][] = $targetValue;
                } else {
                    // Target is the array, push source value and copy back
                    $targetValue[] = $source[$targetKey];
                    $source[$targetKey] = $targetValue;
                }
            } else {
                // No merge required, just set the value
                $source[$targetKey] = $targetValue;
            }
        }
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->config;
    }
}
