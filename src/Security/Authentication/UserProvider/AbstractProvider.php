<?php

declare(strict_types=1);

namespace PHPCensor\Security\Authentication\UserProvider;

use PHPCensor\Security\Authentication\UserProviderInterface;
use PHPCensor\Store\UserStore;
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

    protected UserStore $userStore;

    public function __construct(
        UserStore $userStore,
        string $key,
        array $config
    ) {
        $this->key       = $key;
        $this->config    = $config;
        $this->userStore = $userStore;
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
