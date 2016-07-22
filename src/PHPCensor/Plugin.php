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
    protected $phpci;

    /**
     * @var \PHPCensor\Model\Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Builder $phpci
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        $this->phpci   = $phpci;
        $this->build   = $build;
        $this->options = $options;

        $this->phpci->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * @return boolean
     */
    abstract public function execute();
}
