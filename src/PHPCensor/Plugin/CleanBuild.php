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
 * Clean build removes Composer related files and allows PHPCI users to clean up their build directory.
 * Useful as a precursor to copy_build.
 * 
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class CleanBuild extends Plugin
{
    protected $remove;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'clean_build';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->remove = isset($options['remove']) && is_array($options['remove']) ? $options['remove'] : [];
    }

    /**
    * Executes Composer and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $cmd = 'rm -Rf "%s"';

        $this->builder->executeCommand($cmd, $this->builder->buildPath . 'composer.phar');
        $this->builder->executeCommand($cmd, $this->builder->buildPath . 'composer.lock');

        $success = true;

        foreach ($this->remove as $file) {
            $ok = $this->builder->executeCommand($cmd, $this->builder->buildPath . $file);

            if (!$ok) {
                $success = false;
            }
        }

        return $success;
    }
}
