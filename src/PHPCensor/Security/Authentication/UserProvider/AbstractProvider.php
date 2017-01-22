<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright Copyright 2014, Block 8 Limited.
 * @license   https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link      https://www.phptesting.org/
 */

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
    protected $key;

    /**
     * @var array
     */
    protected $config;

    /**
     * AbstractProvider constructor
     * 
     * @param string $key
     * @param array  $config
     */
    public function __construct($key, array $config)
    {
        $this->key    = $key;
        $this->config = $config;
    }
}
