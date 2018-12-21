<?php

namespace PHPCensor;

use PHPCensor\Model\Build;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
abstract class Plugin
{
    const STATUS_PENDING        = 0;
    const STATUS_RUNNING        = 1;
    const STATUS_SUCCESS        = 2;
    const STATUS_FAILED         = 3;
    const STATUS_FAILED_ALLOWED = 4;

    const AVAILABLE_PRIORITY_PATHS = [
        'global',
        'system',
        'local',
        'binary_path'
    ];

    /**
     * @var \PHPCensor\Builder
     */
    protected $builder;

    /**
     * @var \PHPCensor\Model\Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $priorityPath = 'local';

    /**
     * Manual set the binary directory
     *
     * @var string
     */
    protected $binaryPath = '';

    /**
     * Manual set the binary name
     *
     * @var string
     */
    protected $binaryName = '';

    /**
     * @param Builder $builder
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        $this->builder = $builder;
        $this->build   = $build;
        $this->options = $options;

        // Plugin option overwrite builder options for priority_path and binary_path
        if (
            !empty($options['priority_path']) &&
            in_array($options['priority_path'], self::AVAILABLE_PRIORITY_PATHS, true)
        ) {
            $this->priorityPath = $options['priority_path'];
        } else {
            $this->priorityPath = $this->builder->priorityPath;
        }

        if (!empty($options['binary_path'])) {
            $this->binaryPath = $options['binary_path'];
        } else {
            $this->binaryPath = $this->builder->binaryPath;
        }

        //allow %BUILD_PATH% and other replacements for the directory
        $this->binaryPath = $this->builder->interpolate($this->binaryPath);

        if (!empty($options['binary_name'])) {
            $this->binaryName = $options['binary_name'];
        }

        $this->builder->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * add an ending / and remove the starting /
     *
     * @param array $options
     *
     * @return string
     */
    protected function getWorkingDirectory(array $options)
    {
        $directory = $this->builder->directory;

        if (!empty($options['directory'])) {
            $directory = $options['directory'];
        }

        return rtrim($this->builder->interpolate($directory), '/\\') . '/';
    }

    /**
     * ignorePathRelativeToDirectory
     *
     * Ignore is not managed globaly like binary_path
     * the usage is different per plugin
     *
     * @param string $rootDirectory
     * @param array $list_ignored
     *
     * @return array
     */
    protected function ignorePathRelativeToDirectory($rootDirectory, $list_ignored)
    {
        $rootDirectory = preg_replace('{^\./}', '', $rootDirectory, -1, $count);
        $rootDirectory = rtrim($rootDirectory, "/") . '/';
        if ('/' != $rootDirectory[0]) {
            $rootDirectory = $this->builder->interpolate('%BUILD_PATH%' . $rootDirectory);
        }

        $newIgnored = [];
        // only subdirectory of the defined of $this->directory will be ignored.
        foreach ($list_ignored as $path_to_ignore) {
            // Get absolute Path of the ignored path
            $absolutePathToIgnore = $this->builder->interpolate('%BUILD_PATH%' . $path_to_ignore);
            // We cut ou current directory to have the same size
            $rootInIgnore = substr($absolutePathToIgnore, 0, strlen($rootDirectory));
            if (strcmp($rootDirectory, $rootInIgnore) == 0) {
                //we take the right part to have the relative
                $newIgnored[] = substr($absolutePathToIgnore, strlen($rootInIgnore));
            }
        }

        return $newIgnored;
    }

    /**
     * Find a binary required by a plugin.
     *
     * @param array|string $binary
     *
     * @return string
     *
     * @throws \Exception when no binary has been found.
     */
    public function findBinary($binary)
    {
        return $this->builder->findBinary($binary, $this->priorityPath, $this->binaryPath, $this->binaryName);
    }

    /**
     * @return Build
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return string
     */
    public function getPriorityPath()
    {
        return $this->priorityPath;
    }

    /**
     * @return boolean
     */
    abstract public function execute();

    /**
     * @return string
     */
    public static function pluginName()
    {
        return '';
    }
}
