<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication;

use b8\Config;

/**
 * Authentication facade.
 *
 * @author   Adirelle <adirelle@gmail.com>
 */
class Service
{
    /**
     *
     * @var Service
     */
    static private $instance;

    /** Return the service singletion.
     *
     * @return Service
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $config = Config::getInstance()->get(
                'phpci.security.authentication',
                array('internal' => 'internal')
            );

            $providers = [];
            foreach ($config as $key => $providerConfig) {
                $providers[$key] = self::buildProvider($key, $providerConfig);
            }
            self::$instance = new self($providers);
        }
        return self::$instance;
    }

    /** Create a provider from a given configuration.
     *
     * @param string $key
     * @param string|array $config
     * @return UserProvider
     */
    public static function buildProvider($key, $config)
    {
        if (is_string($config)) {
            $config = array('type' => $config);
        }

        $type = $config['type'];
        if (class_exists($type)) {
            $class = $type;
        } elseif (class_exists('PHPCI\\Security\\Authentication\\UserProvider\\' . $type)) {
            $class = 'PHPCI\\Security\\Authentication\\UserProvider\\' . $type;
        } else {
            // TODO: error
        }

        return new $class($key, $config);
    }

    /** The table of providers.
     *
     * @var array
     */
    private $providers;

    /** Initialize the service.
     *
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /** Return all providers.
     *
     * @return UserProvider[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /** Return the user providers that allows password authentication.
     *
     * @return LoginPasswordProvider[]
     */
    public function getLoginPasswordProviders()
    {
        $providers = [];
        foreach ($this->providers as $key => $provider) {
            if ($provider instanceof LoginPasswordProvider) {
                $providers[$key] = $provider;
            }
        }
        return $providers;
    }
}
