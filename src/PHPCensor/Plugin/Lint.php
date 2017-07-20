<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * PHP Lint Plugin - Provides access to PHP lint functionality.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Lint extends Plugin
{
    protected $directories;
    protected $recursive = true;
    protected $ignore;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'lint';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directories = [''];
        $this->ignore      = $this->builder->ignore;

        if (!empty($options['directory'])) {
            $this->directories[] = $options['directory'];
        }

        if (!empty($options['directories'])) {
            $this->directories = $options['directories'];
        }

        if (array_key_exists('recursive', $options)) {
            $this->recursive = $options['recursive'];
        }
    }

    /**
     * Executes parallel lint
     */
    public function execute()
    {
        $this->builder->quiet = true;
        $success            = true;

        $php = $this->findBinary('php');

        foreach ($this->directories as $dir) {
            if (!$this->lintDirectory($php, $dir)) {
                $success = false;
            }
        }

        $this->builder->quiet = false;

        return $success;
    }

    /**
     * Lint an item (file or directory) by calling the appropriate method.
     * @param $php
     * @param $item
     * @param $itemPath
     * @return bool
     */
    protected function lintItem($php, $item, $itemPath)
    {
        $success = true;

        if ($item->isFile() && $item->getExtension() == 'php' && !$this->lintFile($php, $itemPath)) {
            $success = false;
        } elseif ($item->isDir() && $this->recursive && !$this->lintDirectory($php, $itemPath . DIRECTORY_SEPARATOR)) {
            $success = false;
        }

        return $success;
    }

    /**
     * Run php -l against a directory of files.
     * @param $php
     * @param $path
     * @return bool
     */
    protected function lintDirectory($php, $path)
    {
        $success = true;
        $directory = new \DirectoryIterator($this->builder->buildPath . $path);

        foreach ($directory as $item) {
            if ($item->isDot()) {
                continue;
            }

            $itemPath = $path . $item->getFilename();

            if (in_array($itemPath, $this->ignore)) {
                continue;
            }

            if (!$this->lintItem($php, $item, $itemPath)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Run php -l against a specific file.
     * @param $php
     * @param $path
     * @return bool
     */
    protected function lintFile($php, $path)
    {
        $success = true;

        if (!$this->builder->executeCommand($php . ' -l "%s"', $this->builder->buildPath . $path)) {
            $this->builder->logFailure($path);
            $success = false;
        }

        return $success;
    }
}
