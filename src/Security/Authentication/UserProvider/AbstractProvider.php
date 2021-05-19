<?php

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Security\Authentication\UserProviderInterface;
use PHPCensor\StoreRegistry;

/**
 * Abstract user provider.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
abstract class AbstractProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected string $key;

    /**
     * @var array
     */
    protected array $config;

    protected StoreRegistry $storeRegistry;

    public function __construct(
        StoreRegistry $storeRegistry,
        string $key,
        array $config
    ) {
        $this->key           = $key;
        $this->config        = $config;
        $this->storeRegistry = $storeRegistry;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
