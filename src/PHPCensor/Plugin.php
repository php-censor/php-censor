<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
* PHPCI Plugin class - Used by all build plugins.
 * 
* @author Dan Cryer <dan@block8.co.uk>
*/
abstract class Plugin
{
    /**
     * @var \PHPCensor\Builder
     */
    protected $builder;

    /**
     * @var \PHPCensor\Model\Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Builder $builder
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        $this->builder = $builder;
        $this->build   = $build;
        $this->options = $options;

        $this->builder->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * @return Build
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return boolean
     */
    abstract public function execute();

    /**
     * @return string
     */
    public static function pluginName()
    {
        return '';
    }
}
