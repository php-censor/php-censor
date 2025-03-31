<?php

namespace PHPCensor\Plugin;

use DirectoryIterator;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * PHP Lint Plugin - Provides access to PHP lint functionality.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Lint extends Plugin
{
    protected $directories;
    protected $recursive = true;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'lint';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directories = [
            $this->directory,
        ];

        if (!empty($options['directories']) && \is_array($options['directories'])) {
            foreach ($options['directories'] as $index => $directory) {
                $relativePath = \preg_replace(
                    '#^(\./|/)?(.*)$#',
                    '$2',
                    $options['directories'][$index]
                );
                $relativePath = \rtrim($relativePath, "\//");

                $this->directories[] = $this->builder->buildPath . $relativePath . '/';
            }
        }

        if (\array_key_exists('recursive', $options)) {
            $this->recursive = $options['recursive'];
        }
    }

    /**
     * Executes parallel lint
     */
    public function execute()
    {
        $success = true;

        foreach ($this->directories as $dir) {
            if (!$this->lintDirectory($dir)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Lint an item (file or directory) by calling the appropriate method.
     * @return bool
     */
    protected function lintItem($item, $itemPath)
    {
        $success = true;

        if ($item->isFile() && $item->getExtension() === 'php' && !$this->lintFile($itemPath)) {
            $success = false;
        } elseif ($item->isDir() &&
            $this->recursive &&
            !$this->lintDirectory($itemPath . '/')) {
            $success = false;
        }

        return $success;
    }

    /**
     * Run php -l against a directory of files.
     * @return bool
     */
    protected function lintDirectory($path)
    {
        $success = true;
        $directory = new DirectoryIterator($this->builder->buildPath . $path);

        foreach ($directory as $item) {
            if ($item->isDot()) {
                continue;
            }

            $itemPath = $path . $item->getFilename();

            if (\in_array($itemPath, $this->ignore, true)) {
                continue;
            }

            if (!$this->lintItem($item, $itemPath)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Run php -l against a specific file.
     * @return bool
     */
    protected function lintFile($path)
    {
        $success = true;

        if (!$this->builder->executeCommand(Builder::PHP_CLI_TAG . ' -l "%s"', $this->builder->buildPath . $path)) {
            $this->builder->logFailure($path);
            $success = false;
        }

        return $success;
    }
}
