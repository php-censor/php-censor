<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PDO;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
* SQLite Plugin — Provides access to a SQLite database.
* @author       Corpsee <poisoncorpsee@gmail.com>
* @package      PHPCI
* @subpackage   Plugins
*/
class Sqlite implements Plugin
{
    /**
     * @var \PHPCensor\Builder
     */
    protected $phpci;

    /**
     * @var \PHPCI\Model\Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var string
     */
    protected $path;

    /**
     * @param Builder $phpci
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        $this->phpci   = $phpci;
        $this->build   = $build;
        $this->queries = $options;
        $buildSettings = $phpci->getConfig('build_settings');

        if (isset($buildSettings['sqlite'])) {
            $sql = $buildSettings['sqlite'];
            $this->path = $sql['path'];
        }

        $this->phpci->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * Connects to SQLite and runs a specified set of queries.
     * @return boolean
     */
    public function execute()
    {
        try {
            $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $pdo  = new PDO('sqlite:' . $this->path, $opts);

            foreach ($this->queries as $query) {
                $pdo->query($this->phpci->interpolate($query));
            }
        } catch (\Exception $ex) {
            $this->phpci->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }
}
