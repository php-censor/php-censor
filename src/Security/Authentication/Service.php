<?php

namespace PHPCensor\Security\Authentication;

use PHPCensor\ConfigurationInterface;
use PHPCensor\StoreRegistry;

/**
 * Authentication facade.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Service
{
    /**
     * The table of providers.
     *
     * @var array
     */
    private array $providers;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        array $providers = []
    ) {
        if (!$providers) {
            $config = $configuration->get(
                'php-censor.security.auth_providers',
                [
                    'internal' => [
                        'type' => 'internal'
                    ]
                ]
            );

            $providers = [];
            foreach ($config as $key => $providerConfig) {
                $providers[$key] = self::buildProvider($storeRegistry, $key, $providerConfig);
            }
        }

        $this->providers = $providers;
    }

    public static function buildProvider(StoreRegistry $storeRegistry, string $key, array $config): UserProviderInterface
    {
        $class = ucfirst($config['type']);
        if (class_exists('\\PHPCensor\\Security\\Authentication\\UserProvider\\' . $class)) {
            $class = '\\PHPCensor\\Security\\Authentication\\UserProvider\\' . $class;
        }

        return new $class($storeRegistry, $key, $config);
    }

    /**
     * Return all providers.
     *
     * @return UserProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Return the user providers that allows password authentication.
     *
     * @return LoginPasswordProviderInterface[]
     */
    public function getLoginPasswordProviders()
    {
        $providers = [];
        foreach ($this->providers as $key => $provider) {
            if ($provider instanceof LoginPasswordProviderInterface) {
                $providers[$key] = $provider;
            }
        }
        return $providers;
    }
}
