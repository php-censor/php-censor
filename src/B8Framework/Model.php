<?php

namespace b8;

use b8\Exception\HttpException;
use Symfony\Component\Cache\Simple\ArrayCache;

class Model
{
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
