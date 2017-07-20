<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Gulp Plugin - Provides access to gulp functionality.
 * 
 * @author Dirk Heilig <dirk@heilig-online.com>
 */
class Gulp extends Plugin
{
    protected $directory;
    protected $task;
    protected $preferDist;
    protected $gulp;
    protected $gulpfile;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'gulp';
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
        $this->gulp      = $this->findBinary('gulp');
        $this->gulpfile  = 'gulpfile.js';

        // Handle options:
        if (isset($options['directory'])) {
            $this->directory = $path . DIRECTORY_SEPARATOR . $options['directory'];
        }

        if (isset($options['task'])) {
            $this->task = $options['task'];
        }

        if (isset($options['gulp'])) {
            $this->gulp = $options['gulp'];
        }

        if (isset($options['gulpfile'])) {
            $this->gulpfile = $options['gulpfile'];
        }
    }

    /**
    * Executes gulp and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        // if npm does not work, we cannot use gulp, so we return false
        $cmd = 'cd %s && npm install';
        if (!$this->builder->executeCommand($cmd, $this->directory)) {
            return false;
        }

        // build the gulp command
        $cmd = 'cd %s && ' . $this->gulp;
        $cmd .= ' --no-color';
        $cmd .= ' --gulpfile %s';
        $cmd .= ' %s'; // the task that will be executed

        // and execute it
        return $this->builder->executeCommand($cmd, $this->directory, $this->gulpfile, $this->task);
    }
}
