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
