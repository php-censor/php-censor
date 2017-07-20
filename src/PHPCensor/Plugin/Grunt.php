<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Grunt Plugin - Provides access to grunt functionality.
 * 
 * @author Tobias Tom <t.tom@succont.de>
 */
class Grunt extends Plugin
{
    protected $directory;
    protected $task;
    protected $preferDist;
    protected $grunt;
    protected $gruntfile;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'grunt';
    }
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);
        
        $path            = $this->builder->buildPath;
        $this->directory = $path;
        $this->task      = null;
        $this->grunt     = $this->findBinary('grunt');
        $this->gruntfile = 'Gruntfile.js';

        // Handle options:
        if (isset($options['directory'])) {
            $this->directory = $path . DIRECTORY_SEPARATOR . $options['directory'];
        }

        if (isset($options['task'])) {
            $this->task = $options['task'];
        }

        if (isset($options['grunt'])) {
            $this->grunt = $options['grunt'];
        }

        if (isset($options['gruntfile'])) {
            $this->gruntfile = $options['gruntfile'];
        }
    }

    /**
    * Executes grunt and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        // if npm does not work, we cannot use grunt, so we return false
        $cmd = 'cd %s && npm install';
        if (!$this->builder->executeCommand($cmd, $this->directory)) {
            return false;
        }

        // build the grunt command
        $cmd = 'cd %s && ' . $this->grunt;
        $cmd .= ' --no-color';
        $cmd .= ' --gruntfile %s';
        $cmd .= ' %s'; // the task that will be executed

        // and execute it
        return $this->builder->executeCommand($cmd, $this->directory, $this->gruntfile, $this->task);
    }
}
