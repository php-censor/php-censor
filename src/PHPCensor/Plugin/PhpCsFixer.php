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
 * PHP CS Fixer - Works with the PHP Coding Standards Fixer for testing coding standards.
 * 
 * @author       Gabriel Baker <gabriel@autonomicpilot.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class PhpCsFixer extends Plugin
{
    protected $workingDir = '';
    protected $level      = ' --level=psr2';
    protected $verbose    = '';
    protected $diff       = '';
    protected $levels     = ['psr0', 'psr1', 'psr2', 'symfony'];

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->workingDir = $this->builder->buildPath;
        $this->buildArgs($options);
    }

    /**
     * Run PHP CS Fixer.
     * @return bool
     */
    public function execute()
    {
        $curdir = getcwd();
        chdir($this->workingDir);

        $phpcsfixer = $this->builder->findBinary('php-cs-fixer');

        $cmd = $phpcsfixer . ' fix . %s %s %s';
        $success = $this->builder->executeCommand($cmd, $this->verbose, $this->diff, $this->level);

        chdir($curdir);

        return $success;
    }

    /**
     * Build an args string for PHPCS Fixer.
     * @param $options
     */
    public function buildArgs($options)
    {
        if (isset($options['verbose']) && $options['verbose']) {
            $this->verbose = ' --verbose';
        }

        if (isset($options['diff']) && $options['diff']) {
            $this->diff = ' --diff';
        }

        if (isset($options['level']) && in_array($options['level'], $this->levels)) {
            $this->level = ' --level='.$options['level'];
        }

        if (isset($options['workingdir']) && $options['workingdir']) {
            $this->workingDir = $this->builder->buildPath . $options['workingdir'];
        }

    }
}
