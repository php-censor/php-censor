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
* Gulp Plugin - Provides access to gulp functionality.
* @author       Dirk Heilig <dirk@heilig-online.com>
* @package      PHPCI
* @subpackage   Plugins
*/
class Gulp extends Plugin
{
    protected $directory;
    protected $task;
    protected $preferDist;
    protected $gulp;
    protected $gulpfile;

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        parent::__construct($phpci, $build, $options);
        
        $path            = $this->phpci->buildPath;
        $this->directory = $path;
        $this->task      = null;
        $this->gulp      = $this->phpci->findBinary('gulp');
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
        if (IS_WIN) {
            $cmd = 'cd /d %s && npm install';
        }
        if (!$this->phpci->executeCommand($cmd, $this->directory)) {
            return false;
        }

        // build the gulp command
        $cmd = 'cd %s && ' . $this->gulp;
        if (IS_WIN) {
            $cmd = 'cd /d %s && ' . $this->gulp;
        }
        $cmd .= ' --no-color';
        $cmd .= ' --gulpfile %s';
        $cmd .= ' %s'; // the task that will be executed

        // and execute it
        return $this->phpci->executeCommand($cmd, $this->directory, $this->gulpfile, $this->task);
    }
}
