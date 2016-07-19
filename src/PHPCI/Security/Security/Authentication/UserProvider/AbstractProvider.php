<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Security\Authentication\UserProvider;

use PHPCI\Security\Authentication\UserProvider;

/**
 * Abstract user provider.
 *
 * @author   Adirelle <adirelle@gmail.com>
 */
abstract class AbstractProvider implements UserProvider
{
    /**
     * @var string
     */
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
