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

        $this->directories = [
            $this->getWorkingDirectory($options)
        ];

        $this->ignore = $this->builder->ignore;

        if (!empty($options['directories']) && is_array($options['directories'])) {
            foreach ($options['directories'] as $index => $directory) {
                $relativePath = preg_replace(
                    '#^(\./|/)?(.*)$#',
                    '$2',
                    $options['directories'][$index]
                );
                $relativePath = rtrim($relativePath, "\//");

                $this->directories[] = $this->builder->buildPath . $relativePath . '/';
            }
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
        $success = true;

        $php = $this->findBinary('php');

        foreach ($this->directories as $dir) {
            if (!$this->lintDirectory($php, $dir)) {
                $success = false;
            }
        }

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
        } elseif (
            $item->isDir() &&
            $this->recursive &&
            !$this->lintDirectory($php, ($itemPath . '/'))
        ) {
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
