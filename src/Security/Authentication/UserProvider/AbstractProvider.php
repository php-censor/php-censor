<?php

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Security\Authentication\UserProviderInterface;

/**
 * Abstract user provider.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
abstract class AbstractProvider implements UserProviderInterface
{
    protected string $key;

    protected array $config;

    /**
     * AbstractProvider constructor
     */
    public function __construct(string $key, array $config)
    {
        $this->key    = $key;
        $this->config = $config;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
