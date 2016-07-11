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
* Wipe Plugin - Wipes a folder
* @author       Claus Due <claus@namelesscoder.net>
* @package      PHPCI
* @subpackage   Plugins
*/
class Wipe extends Plugin
{
    protected $directory;

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        parent::__construct($phpci, $build, $options);
        
        $path            = $this->phpci->buildPath;
        $this->directory = isset($options['directory']) ? $this->phpci->interpolate($options['directory']) : $path;
    }

    /**
    * Wipes a directory's contents
    */
    public function execute()
    {
        $build = $this->phpci->buildPath;

        if ($this->directory == $build || empty($this->directory)) {
            return true;
        }
        if (is_dir($this->directory)) {
            $cmd = 'rm -Rf "%s"';
            if (IS_WIN) {
                $cmd = 'rmdir /S /Q "%s"';
            }
            return $this->phpci->executeCommand($cmd, $this->directory);
        }
        return true;
    }
}
