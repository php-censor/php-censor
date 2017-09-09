<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * Php Parallel Lint Plugin - Provides access to PHP lint functionality.
 * 
 * @author Vaclav Makes <vaclav@makes.cz>
 */
class PhpParallelLint extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array - paths to ignore
     */
    protected $ignore;

    /**
     * @var string - comma separated list of file extensions
     */
    protected $extensions;

    /**
     * @var bool - enable short tags
     */
    protected $shortTag;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_parallel_lint';
    }

    /**
     * $options['directory']  Output Directory. Default: %BUILDPATH%
     * $options['filename']   Phar Filename. Default: build.phar
     * $options['extensions'] Filename extensions. Default: php
     * $options['shorttags']  Enable short tags. Default: false
     * $options['stub']       Stub Content. No Default Value
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory  = $this->builder->buildPath;
        $this->ignore     = $this->builder->ignore;
        $this->extensions = 'php';
        $this->shortTag   = false;

        if (isset($options['directory'])) {
            $this->directory = $this->builder->buildPath.$options['directory'];
        }

        if (isset($options['ignore'])) {
            $this->ignore = $options['ignore'];
        }

        if (isset($options['shorttags'])) {
            $this->shortTag = (strtolower($options['shorttags']) == 'true');
        }

        if (isset($options['extensions'])) {
            // Only use if this is a comma delimited list
            $pattern = '/^([a-z]+)(,\ *[a-z]*)*$/';

            if (preg_match($pattern, $options['extensions'])) {
                $this->extensions = str_replace(' ', '', $options['extensions']);
            }
        }
    }

    /**
     * Check if this plugin can be executed.
     *
     * @param $stage
     * @param Builder $builder
     * @param Build   $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == Build::STAGE_TEST) {
            return true;
        }

        return false;
    }

    /**
    * Executes parallel lint
    */
    public function execute()
    {
        list($ignore) = $this->getFlags();

        $phplint = $this->findBinary('parallel-lint');

        $cmd     = $phplint . ' -e %s' . '%s %s "%s"';
        $success = $this->builder->executeCommand(
            $cmd,
            $this->extensions,
            ($this->shortTag ? ' -s' : ''),
            $ignore,
            $this->directory
        );

        $output = $this->builder->getLastOutput();

        $matches = [];
        if (preg_match_all('/Parse error\:/', $output, $matches)) {
            $this->build->storeMeta('phplint-errors', count($matches[0]));
        }

        return $success;
    }

    /**
     * Produce an argument string for PHP Parallel Lint.
     * @return array
     */
    protected function getFlags()
    {
        $ignoreFlags = [];
        foreach ($this->ignore as $ignoreDir) {
            $ignoreFlags[] = '--exclude "' . $this->builder->buildPath . $ignoreDir . '"';
        }
        $ignore = implode(' ', $ignoreFlags);

        return [$ignore];
    }
}
