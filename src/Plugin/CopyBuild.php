<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use RuntimeException;

/**
 * Copy Build Plugin - Copies the entire build to another directory.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class CopyBuild extends Plugin
{
    protected $respectIgnore;
    protected $wipe;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'copy_build';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->wipe          = isset($options['wipe']) ?  (bool)$options['wipe'] : false;
        $this->respectIgnore = isset($options['respect_ignore']) ?  (bool)$options['respect_ignore'] : false;
    }

    /**
     * Copies files from the root of the build directory into the target folder
     *
     * @return bool
     * @throws RuntimeException
     */
    public function execute()
    {
        $buildPath = $this->builder->buildPath;

        if ($this->directory === $buildPath) {
            return false;
        }

        $this->wipeExistingDirectory();

        if (is_dir($this->directory)) {
            throw new RuntimeException(
                sprintf(
                    'Directory "%s" already exists! Use "wipe" option if you want to delete directory before copy.',
                    $this->directory
                )
            );
        }

        $cmd     = 'cd "%s" && mkdir -p "%s" && cp -R %s/. "%s"';
        $success = $this->builder->executeCommand($cmd, $buildPath, $this->directory, rtrim($buildPath, '/'), $this->directory);
        
        $this->deleteIgnoredFiles();

        return $success;
    }

    /**
     * Wipe the destination directory if it already exists.
     *
     * @throws RuntimeException
     */
    protected function wipeExistingDirectory()
    {
        if ($this->wipe === true && $this->directory !== '/' && is_dir($this->directory)) {
            $cmd = 'cd "%s" && rm -Rf "%s"';
            $success = $this->builder->executeCommand($cmd, $this->builder->buildPath, $this->directory);

            if (!$success) {
                throw new RuntimeException(
                    sprintf('Failed to wipe existing directory "%s" before copy!', $this->directory)
                );
            }

            clearstatcache();
        }
    }

    /**
     * Delete any ignored files from the build prior to copying.
     */
    protected function deleteIgnoredFiles()
    {
        if ($this->respectIgnore) {
            foreach ($this->builder->ignore as $file) {
                $cmd = 'cd "%s" && rm -Rf "%s/%s"';
                $this->builder->executeCommand($cmd, $this->builder->buildPath, $this->directory, $file);
            }
        }
    }
}
