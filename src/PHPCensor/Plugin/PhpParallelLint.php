<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
* Php Parallel Lint Plugin - Provides access to PHP lint functionality.
* @author       Vaclav Makes <vaclav@makes.cz>
* @package      PHPCI
* @subpackage   Plugins
*/
class PhpParallelLint extends Plugin
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
     * $options['directory']  Output Directory. Default: %BUILDPATH%
     * $options['filename']   Phar Filename. Default: build.phar
     * $options['extensions'] Filename extensions. Default: php
     * $options['stub']       Stub Content. No Default Value
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory  = $this->builder->buildPath;
        $this->ignore     = $this->builder->ignore;
        $this->extensions = 'php';

        if (isset($options['directory'])) {
            $this->directory = $this->builder->buildPath.$options['directory'];
        }

        if (isset($options['ignore'])) {
            $this->ignore = $options['ignore'];
        }

        if (isset($options['extensions'])) {
            $this->extensions = $options['extensions'];
        }
    }

    /**
    * Executes parallel lint
    */
    public function execute()
    {
        list($ignore) = $this->getFlags();

        $phplint = $this->builder->findBinary('parallel-lint');

        $cmd     = $phplint . ' -e %s' . ' %s "%s"';
        $success = $this->builder->executeCommand(
            $cmd,
            $this->extensions,
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
