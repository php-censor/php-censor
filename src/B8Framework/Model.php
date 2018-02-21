<?php

namespace b8;

use b8\Exception\HttpException;
use Symfony\Component\Cache\Simple\ArrayCache;

class Model
{
    public static $sleepable = [];
    protected $getters       = [];
    protected $setters       = [];
    protected $data          = [];
    protected $modified      = [];
    protected $tableName;
    protected $cache;

    /**
     * @param array $initialData
     */
    public function __construct($initialData = [])
    {
        if (is_array($initialData)) {
            $this->data = array_merge($this->data, $initialData);
        }

        $this->cache = new ArrayCache();
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param integer $depth
     * @param integer $currentDepth
     *
     * @return array
     */
    public function toArray($depth = 2, $currentDepth = 0)
    {
        if (isset(static::$sleepable) && is_array(static::$sleepable) && count(static::$sleepable)) {
            $sleepable = static::$sleepable;
        } else {
            $sleepable = array_keys($this->getters);
        }

        $rtn = [];
        foreach ($sleepable as $property) {
            $rtn[$property] = $this->propertyToArray($property, $currentDepth, $depth);
        }

        return $rtn;
    }

    /**
     * @param string  $property
     * @param integer $currentDepth
     * @param integer $depth
     *
     * @return mixed
     */
    protected function propertyToArray($property, $currentDepth, $depth)
    {
        $rtn = null;

        if (array_key_exists($property, $this->getters)) {
            $method = $this->getters[$property];
            $rtn = $this->{$method}();

            if (is_object($rtn) || is_array($rtn)) {
                $rtn = ($depth > $currentDepth) ? $this->valueToArray($rtn, $currentDepth, $depth) : null;
            }
        }

        return $rtn;
    }

    /**
     * @param mixed   $value
     * @param integer $currentDepth
     * @param integer $depth
     *
     * @return mixed
     */
    protected function valueToArray($value, $currentDepth, $depth)
    {
        $rtn = null;
        if (!is_null($value)) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $rtn = $value->toArray($depth, $currentDepth + 1);
            } elseif (is_array($value)) {
                $childArray = [];

                foreach ($value as $k => $v) {
                    $childArray[$k] = $this->valueToArray($v, $currentDepth + 1, $depth);
                }

                $rtn = $childArray;
            } else {
                $rtn = (is_string($value) && !mb_check_encoding($value, 'UTF-8'))
                    ? mb_convert_encoding($value, 'UTF-8')
                    : $value;
            }
        }

        return $rtn;
    }

    /**
     * @return array
     */
    public function getDataArray()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (isset($this->setters[$key])) {
                $func = $this->setters[$key];

                if ($value === 'null') {
                    $value = null;
                } elseif ($value === 'true') {
                    $value = true;
                } elseif ($value === 'false') {
                    $value = false;
                }

                $this->{$func}($value);
            }
        }
    }

    /**
     * @param string $column
     */
    protected function setModified($column)
    {
        $this->modified[$column] = $column;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws HttpException\ValidationException
     */
    protected function validateString($name, $value)
    {
        if (!is_string($value) && !is_null($value)) {
            throw new HttpException\ValidationException('Column "', $name . '" must be a string.');
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws HttpException\ValidationException
     */
    protected function validateInt($name, &$value)
    {
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }

        if (!is_numeric($value) && !is_null($value)) {
            throw new HttpException\ValidationException('Column "', $name . '" must be an integer.');
        }

        if (!is_int($value) && !is_null($value)) {
            $value = (int)$value;
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws HttpException\ValidationException
     */
    protected function validateFloat($name, &$value)
    {
        if (!is_numeric($value) && !is_null($value)) {
            throw new HttpException\ValidationException('Column "', $name . '" must be a float.');
        }

        if (!is_float($value) && !is_null($value)) {
            $value = (float)$value;
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws HttpException\ValidationException
     */
    protected function validateDate($name, &$value)
    {
        if (is_string($value)) {
            $value = empty($value) ? null : new \DateTime($value);
        }

        if ((!is_object($value) || !($value instanceof \DateTime)) && !is_null($value)) {
            throw new HttpException\ValidationException('Column "', $name . '" must be a date object.');
        }

        $value = empty($value) ? null : $value->format('Y-m-d H:i:s');
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws HttpException\ValidationException
     */
    protected function validateNotNull($name, $value)
    {
        if (is_null($value)) {
            throw new HttpException\ValidationException('Column "', $name . '" must not be null.');
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->getters)) {
            $getter = $this->getters[$key];
            return $this->{$getter}();
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->setters)) {
            $setter = $this->setters[$key];
            return $this->{$setter}($value);
        }
    }
}
