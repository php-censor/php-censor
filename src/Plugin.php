<?php

namespace PHPCensor;

use Exception;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\Codeception;

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
     * @var Builder
     */
    protected $builder;

    /**
     * @var Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string[]
     */
    protected $ignore;

    /**
     * @var string
     */
    protected $executable;

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
     * @var array
     */
    protected $binaryName = [];

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

        $this->directory  = $this->normalizeDirectory();
        $this->binaryPath = $this->normalizeBinaryPath();
        $this->ignore     = $this->normalizeIgnore();

        // Plugin option overwrite builder options for priority_path and binary_path
        if (!empty($options['priority_path']) &&
            in_array($options['priority_path'], self::AVAILABLE_PRIORITY_PATHS, true)) {
            $this->priorityPath = $options['priority_path'];
        } else {
            $this->priorityPath = $this->builder->priorityPath;
        }

        if (!empty($options['binary_name'])) {
            if (is_array($options['binary_name'])) {
                $this->binaryName = $options['binary_name'];
            } else {
                $this->binaryName = [(string)$options['binary_name']];
            }
        }

        $this->builder->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * @param string $rawPath
     *
     * @return string
     */
    protected function normalizePath($rawPath)
    {
        $normalizedPath = $this->builder->interpolate($rawPath);

        if ('/' !== substr($rawPath, 0, 1)) {
            $normalizedPath = $this->build->getBuildPath() . $normalizedPath;
        }

        $realPath = realpath($normalizedPath);

        return (false !== $realPath)
            ? rtrim($realPath, '/\\') . '/'
            : rtrim(
                str_replace(
                    '//',
                    '/',
                    str_replace('/./', '/', $normalizedPath)
                ),
                '/\\'
            ) . '/';
    }

    /**
     * @return string
     */
    protected function normalizeBinaryPath()
    {
        $binaryPath = '';
        if (!empty($this->options['binary_path'])) {
            $optionBinaryPath = $this->builder->interpolate($this->options['binary_path']);

            if ('/' !== substr($optionBinaryPath, 0, 1)) {
                $binaryPath = $this->build->getBuildPath();
            }

            $binaryPath .= $optionBinaryPath;
        } else {
            $binaryPath = $this->builder->binaryPath;
        }

        $realPath = realpath($binaryPath);

        return (false !== $realPath)
            ? rtrim($realPath, '/\\') . '/'
            : rtrim(
                str_replace(
                    '//',
                    '/',
                    str_replace('/./', '/', $binaryPath)
                ),
                '/\\'
            ) . '/';
    }

    /**
     * @return string
     */
    protected function normalizeDirectory()
    {
        if (!empty($this->options['directory']) && is_array($this->options['directory'])) {
            return $this->builder->directory;
        }

        $directory = '';
        if (!empty($this->options['directory'])) {
            $optionDirectory = $this->builder->interpolate($this->options['directory']);

            if ('/' !== substr($optionDirectory, 0, 1)) {
                $directory = $this->build->getBuildPath();
            }

            $directory .= $optionDirectory;
        } else {
            $directory = $this->builder->directory;
        }

        $realPath = realpath($directory);

        $finalDirectory = (false !== $realPath)
            ? rtrim($realPath, '/\\') . '/'
            : rtrim(
                str_replace(
                    '//',
                    '/',
                    str_replace('/./', '/', $directory)
                ),
                '/\\'
            ) . '/';

        $this->builder->logDebug('Directory: ' . $finalDirectory);

        return $finalDirectory;
    }

    /**
     * @return array
     */
    protected function normalizeIgnore()
    {
        $ignore = $this->builder->ignore;

        if (!empty($this->options['ignore'])) {
            $ignore = array_merge($ignore, $this->options['ignore']);
        }

        $baseDirectory = $this->builder->buildPath;

        array_walk($ignore, function (&$value) use ($baseDirectory) {
            $value = $this->builder->interpolate($value);

            if ('/' !== substr($value, 0, 1)) {
                $value = $baseDirectory . $value;
            }

            clearstatcache(true);
            $realPath = realpath($value);

            $value = (false !== $realPath)
                ? $realPath
                : $value;

            $value = str_replace("/./", '/', $value);
            $value = rtrim(
                str_replace(
                    '//',
                    '/',
                    str_replace($baseDirectory, '', $value)
                ),
                '/\\'
            );
        });

        return array_unique($ignore);
    }

    /**
     * Find a binary required by a plugin.
     *
     * @param array|string $binary
     *
     * @return string
     *
     * @throws Exception when no binary has been found.
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
     * @return bool
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
