<?php

namespace PHPCensor\Security\Authentication;

use b8\Config;

/**
 * Authentication facade.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Service
{
    /**
     * @var Service
     */
    static private $instance;

    /**
     * Return the service singleton.
     *
     * @return Service
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $config = Config::getInstance()->get(
                'php-censor.security.auth_providers',
                [
                    'internal' => [
                        'type' => 'internal'
                    ]
                ]
            );

            $providers = [];
            foreach ($config as $key => $providerConfig) {
                $providers[$key] = self::buildProvider($key, $providerConfig);
            }
            self::$instance = new self($providers);
        }

        return self::$instance;
    }

    /**
     * Create a provider from a given configuration.
     *
     * @param string       $key
     * @param string|array $config
     *
     * @return UserProviderInterface
     */
    public static function buildProvider($key, $config)
    {
        $class = ucfirst($config['type']);
        if (class_exists('\\PHPCensor\\Security\\Authentication\\UserProvider\\' . $class)) {
            $class = '\\PHPCensor\\Security\\Authentication\\UserProvider\\' . $class;
        }

        return new $class($key, $config);
    }

    /**
     * The table of providers.
     *
     * @var array
     */
    private $providers;

    /**
     * Initialize the service.
     *
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
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
