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

        foreach ($initialData as $column => $value) {
            $this->setData($column, $this->cast($this->getCast($column), $value));
        }

        $this->storeRegistry = $storeRegistry;
    }

    public function getCast(string $column): string
    {
        return $this->casts[$column] ?? 'string';
    }

    protected function getData(string $column, $defaultValue = null)
    {
        return $this->cast($this->getCast($column), $this->data[$column] ?? $defaultValue);
    }

    protected function setData(string $column, $value): bool
    {
        if (!array_key_exists($column, $this->data) || $this->data[$column] === $value) {
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

        switch ($type) {
            case 'int':
            case 'integer':
                return is_integer($value) ? $value : intval($value);

            case 'bool':
            case 'boolean':
                return is_bool($value) ? $value : boolval($value);

            case 'float':
                return is_float($value) ? $value : floatval($value);

            case 'array':
                return is_array($value) ? $value : json_decode($value, true);

            case 'datetime':
                if (is_a($value, DateTime::class)) {
                    return $value;
                }
                try {
                    return new DateTime($value);
                } catch (Exception $e) {
                    return null;
                }

            case 'newline':
                if (!is_string($value)) {
                    return $value;
                }

                return array_values(
                    array_filter(
                        array_map(
                            'trim',
                            explode("\n", $value)
                        )
                    )
                );


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
