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

    public function getDataArray(): array
    {
        return $this->data;
    }

    public function getModified(): array
    {
        return $this->modified;
    }

    protected function setModified(string $column): bool
    {
        $this->modified[$column] = $column;

        return true;
    }
}
