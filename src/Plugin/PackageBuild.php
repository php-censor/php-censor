<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Create a ZIP or TAR.GZ archive of the entire build.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class PackageBuild extends Plugin
{
    protected $directory;
    protected $filename;
    protected $format;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'package_build';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory = $this->getWorkingDirectory($options);

        $this->filename  = isset($options['filename']) ? $options['filename'] : 'build';
        $this->format    = isset($options['format']) ?  $options['format'] : 'zip';
    }

    /**
    * Executes Composer and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $path  = $this->builder->buildPath;
        $build = $this->build;

        if ($this->directory == $path) {
            return false;
        }

        $filename = str_replace('%build.commit%', $build->getCommitId(), $this->filename);
        $filename = str_replace('%build.id%', $build->getId(), $filename);
        $filename = str_replace('%build.branch%', $build->getBranch(), $filename);
        $filename = str_replace('%project.title%', $build->getProject()->getTitle(), $filename);
        $filename = str_replace('%date%', date('Y-m-d'), $filename);
        $filename = str_replace('%time%', date('Hi'), $filename);
        $filename = preg_replace('/([^a-zA-Z0-9_-]+)/', '', $filename);

        $currentDir = getcwd();
        chdir($this->builder->buildPath);

        if (!is_array($this->format)) {
            $this->format = [$this->format];
        }

        foreach ($this->format as $format) {
            switch ($format) {
                case 'tar':
                    $cmd = 'tar cfz "%s/%s.tar.gz" ./*';
                    break;
                default:
                case 'zip':
                    $cmd = 'zip -rq "%s/%s.zip" ./*';
                    break;
            }

            $success = $this->builder->executeCommand($cmd, $this->directory, $filename);
        }

        chdir($currentDir);

        return $success;
    }
}
