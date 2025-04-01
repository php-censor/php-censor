<?php

declare(strict_types=1);

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
    public function __construct(
        protected StoreRegistry $storeRegistry,
        protected string $key,
        protected array $config
    ) {
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
