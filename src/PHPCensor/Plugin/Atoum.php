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
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Atoum plugin, runs Atoum tests within a project.
 * 
 * @package PHPCensor\Plugin
 */
class Atoum extends Plugin
{
    protected $executable;
    protected $args;
    protected $config;
    protected $directory;

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (isset($options['executable'])) {
            $this->executable = $this->builder->buildPath . DIRECTORY_SEPARATOR.$options['executable'];
        } else {
            $this->executable = $this->builder->findBinary('atoum');
        }

        if (isset($options['args'])) {
            $this->args = $options['args'];
        }

        if (isset($options['config'])) {
            $this->config = $options['config'];
        }

        if (isset($options['directory'])) {
            $this->directory = $options['directory'];
        }
    }

    /**
     * Run the Atoum plugin.
     * @return bool
     */
    public function execute()
    {
        $cmd = $this->executable;

        if ($this->args !== null) {
            $cmd .= " {$this->args}";
        }
        if ($this->config !== null) {
            $cmd .= " -c '{$this->config}'";
        }
        if ($this->directory !== null) {
            $dirPath = $this->builder->buildPath . DIRECTORY_SEPARATOR . $this->directory;
            $cmd .= " -d '{$dirPath}'";
        }
        chdir($this->builder->buildPath);
        $output = '';
        $status = true;
        exec($cmd, $output);

        if (count(preg_grep("/Success \(/", $output)) == 0) {
            $status = false;
            $this->builder->log($output);
        }
        if (count($output) == 0) {
            $status = false;
            $this->builder->log(Lang::get('no_tests_performed'));
        }
        
        return $status;
    }
}
