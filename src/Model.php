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

        foreach ($initialData as $index => $item) {
            if (!array_key_exists($index, $this->data)) {
                continue;
            }

            $this->data[$index] = $item;
        }

        $this->storeRegistry = $storeRegistry;
    }

    protected function getData(string $column, $defaultValue = null)
    {
        return $this->cast($this->casts[$column] ?? 'string', $this->data[$column] ?? $defaultValue);
    }

    protected function setData(string $column, $value): bool
    {
        $stringValue = $value;

        if (!is_null($value)) {
            switch ($this->casts[$column] ?? 'string') {
                case 'datetime':
                    $stringValue = $value->format('Y-m-d H:i:s');
                    break;
                case 'array':
                    $stringValue = json_encode($value);
                    break;
            }
        }

        if ($this->data[$column] === $stringValue) {
            return false;
        }

        $this->data[$column] = $stringValue;
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->getData('id');
    }

    /**
     * @param int $value
     * @return bool
     */
    public function setId(int $value): bool
    {
        return $this->setData('id', $value);
    }
}
