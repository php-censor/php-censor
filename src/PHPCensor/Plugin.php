<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * PHPCI Plugin class - Used by all build plugins.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
abstract class Plugin
{
    const STATUS_PENDING        = 0;
    const STATUS_RUNNING        = 1;
    const STATUS_SUCCESS        = 2;
    const STATUS_FAILED         = 3;
    const STATUS_FAILED_ALLOWED = 4;
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
