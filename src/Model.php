<?php

declare(strict_types=1);

namespace PHPCensor;

class Model
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $modified = [];

    /**
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
        if (is_array($initialData)) {
            foreach ($initialData as $index => $item) {
                if (array_key_exists($index, $this->data)) {
                    $this->data[$index] = $item;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getDataArray(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getModified(): array
    {
        return $this->modified;
    }

    /**
     * @param string $column
     *
     * @return bool
     */
    protected function setModified(string $column): bool
    {
        $this->modified[$column] = $column;

        return true;
    }
}
