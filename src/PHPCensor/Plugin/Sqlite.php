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
 * 
 * @author       Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Sqlite extends Plugin
{
    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $buildSettings = $this->builder->getConfig('build_settings');

        if (isset($buildSettings['sqlite'])) {
            $sql        = $buildSettings['sqlite'];
            $this->path = $sql['path'];
        }
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
                $pdo->query($this->builder->interpolate($query));
            }
        } catch (\Exception $ex) {
            $this->builder->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }
}
