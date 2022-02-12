<?php

declare(strict_types=1);

namespace PHPCensor;

use DateTime;
use Exception;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Model
{
    protected array $data = [];

    protected array $casts = [];

    private array $modified = [];

    protected StoreRegistry $storeRegistry;

    public function __construct(
        StoreRegistry $storeRegistry,
        array $initialData = []
    ) {
        if (!isset($this->casts['id'])) {
            $this->casts['id'] = 'integer';
        }

        $initialData = array_merge($this->data, $initialData);

        foreach ($initialData as $column => $value) {
            if (!array_key_exists($column, $this->data)) {
                continue;
            }

            $this->data[$column] = $this->castToDatabase($this->getCast($column), $value);
        }

        $this->storeRegistry = $storeRegistry;
    }

    protected function getCast(string $column)
    {
        return $this->casts[$column] ?? 'string';
    }

    protected function getData(string $column, $defaultValue = null)
    {
        return $this->cast($this->getCast($column), $this->data[$column] ?? $defaultValue);
    }

    protected function setData(string $column, $value): bool
    {
        if (!is_null($value)) {
            $value = $this->castToDatabase($this->getCast($column), $value);
        }

        if ($this->data[$column] === $value) {
            return false;
        }

        $this->data[$column] = $value;
        $this->modified[$column] = $column;

        return true;
    }

    public function getDataArray(): array
    {
        return $this->data;
    }

    public function getModified(): array
    {
        return $this->modified;
    }

    /**
     * @return mixed
     */
    private function cast(string $type, $value)
    {
        if ($value === null) {
            return null;
        }

        if (gettype($value) === $type) {
            return $value;
        }

        switch ($type) {
            case 'integer':
                return intval($value);

            case 'boolean':
                return boolval($value);

            case 'float':
                return floatval($value);

            case 'array':
                return json_decode($value, true);

            case 'datetime':
                try {
                    return new DateTime($value);
                } catch (Exception $e) {
                    return null;
                }

            default:
                return $value;
        }
    }

    /**
     * @return mixed
     */
    private function castToDatabase(string $type, $value)
    {
        if (gettype($value) === 'string') {
            return $value;
        }

        switch ($type) {
            case 'datetime':
                return $value->format('Y-m-d H:i:s');
            case 'array':
                return json_encode($value);
            default:
                return $value;
        }
    }

    public function getId(): ?int
    {
        return $this->getData('id');
    }

    public function setId(int $value): bool
    {
        return $this->setData('id', $value);
    }
}
