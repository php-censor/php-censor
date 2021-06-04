<?php

declare(strict_types = 1);

namespace PHPCensor;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Model
{
    protected array $data = [];

    protected array $modified = [];

    protected StoreRegistry $storeRegistry;

    public function __construct(
        StoreRegistry $storeRegistry,
        array $initialData = []
    ) {
        if (\is_array($initialData)) {
            foreach ($initialData as $index => $item) {
                if (\array_key_exists($index, $this->data)) {
                    $this->data[$index] = $item;
                }
            }
        }

        $this->storeRegistry = $storeRegistry;
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
