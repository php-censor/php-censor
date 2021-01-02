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
    /**
     * @var string
     */
    protected string $key;

    /**
     * @var array
     */
    protected array $config;

    /**
     * AbstractProvider constructor
     *
     * @param string $key
     * @param array  $config
     */
    public function __construct(string $key, array $config)
    {
        $this->key    = $key;
        $this->config = $config;
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
