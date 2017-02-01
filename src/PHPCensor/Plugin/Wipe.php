<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Wipe Plugin - Wipes a folder
 * 
 * @author       Claus Due <claus@namelesscoder.net>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Wipe extends Plugin
{
    protected $directory;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'wipe';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);
        
        $path            = $this->builder->buildPath;
        $this->directory = isset($options['directory']) ? $this->builder->interpolate($options['directory']) : $path;
    }

    /**
    * Wipes a directory's contents
    */
    public function execute()
    {
        $build = $this->builder->buildPath;

        if ($this->directory == $build || empty($this->directory)) {
            return true;
        }
        if (is_dir($this->directory)) {
            $cmd = 'rm -Rf "%s"';

            return $this->builder->executeCommand($cmd, $this->directory);
        }

        return true;
    }
}
