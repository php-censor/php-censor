<?php

declare(strict_types = 1);

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Security\Authentication\UserProviderInterface;
use PHPCensor\StoreRegistry;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Adirelle <adirelle@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class AbstractProvider implements UserProviderInterface
{
    protected string $key;

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

    public function getKey(): string
    {
        return $this->key;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
