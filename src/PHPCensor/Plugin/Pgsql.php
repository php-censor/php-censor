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
* PgSQL Plugin - Provides access to a PgSQL database.
* @author       Dan Cryer <dan@block8.co.uk>
* @package      PHPCI
* @subpackage   Plugins
*/
class Pgsql implements Plugin
{
    /**
     * @var \PHPCensor\Builder
     */
    protected $phpci;

    /**
     * @var \PHPCensor\Model\Build
     */
    protected $build;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $pass;

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

        if (isset($buildSettings['pgsql'])) {
            $sql = $buildSettings['pgsql'];
            $this->host = $sql['host'];
            $this->user = $sql['user'];
            $this->pass = $sql['pass'];
        }

        $this->phpci->logDebug('Plugin options: ' . json_encode($options));
    }

    /**
     * Connects to PgSQL and runs a specified set of queries.
     * @return boolean
     */
    public function execute()
    {
        try {
            $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $pdo  = new PDO('pgsql:host=' . $this->host, $this->user, $this->pass, $opts);

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
