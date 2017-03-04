<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Copy Build Plugin - Copies the entire build to another directory.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class CopyBuild extends Plugin
{
    protected $directory;
    protected $ignore;
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
        
        $path            = $this->builder->buildPath;
        $this->directory = isset($options['directory']) ? $options['directory'] : $path;
        $this->wipe      = isset($options['wipe']) ?  (bool)$options['wipe'] : false;
        $this->ignore    = isset($options['respect_ignore']) ?  (bool)$options['respect_ignore'] : false;
    }

    /**
    * Copies files from the root of the build directory into the target folder
    */
    public function execute()
    {
        $build = $this->builder->buildPath;

        if ($this->directory == $build) {
            return false;
        }

        $this->wipeExistingDirectory();

        $cmd = 'mkdir -p "%s" && cp -R "%s" "%s"';

        $success = $this->builder->executeCommand($cmd, $this->directory, $build, $this->directory);

        $this->deleteIgnoredFiles();

        return $success;
    }

    /**
     * Wipe the destination directory if it already exists.
     * @throws \Exception
     */
    protected function wipeExistingDirectory()
    {
        if ($this->wipe === true && $this->directory != '/' && is_dir($this->directory)) {
            $cmd = 'rm -Rf "%s*"';
            $success = $this->builder->executeCommand($cmd, $this->directory);

            if (!$success) {
                throw new \Exception(sprintf('Failed to wipe existing directory %s before copy', $this->directory));
            }
        }
    }

    /**
     * Delete any ignored files from the build prior to copying.
     */
    protected function deleteIgnoredFiles()
    {
        if ($this->ignore) {
            foreach ($this->builder->ignore as $file) {
                $cmd = 'rm -Rf "%s/%s"';
                $this->builder->executeCommand($cmd, $this->directory, $file);
            }
        }
    }
}
