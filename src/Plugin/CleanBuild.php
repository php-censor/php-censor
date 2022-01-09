<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Clean build removes Composer related files and allows users to clean up their build directory.
 * Useful as a precursor to copy_build.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class CleanBuild extends Plugin
{
    protected $removeFiles;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'clean_build';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->removeFiles = isset($options['remove']) && \is_array($options['remove']) ? $options['remove'] : [];
    }

    /**
    * Executes Composer and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $cmd = 'rm -Rf "%s"';

        $success = true;

        foreach ($this->removeFiles as $file) {
            $ok = $this->builder->executeCommand($cmd, $this->builder->buildPath . $file);

            if (!$ok) {
                $success = false;
            }
        }

        return $success;
    }
}
