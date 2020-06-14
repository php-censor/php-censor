<?php

namespace PHPCensor\Plugin;

use Exception;
use PDO;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * PgSQL Plugin - Provides access to a PgSQL database.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Pgsql extends Plugin
{
    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 5432;

    /**
     * @var string|null
     */
    protected $dbName = null;

    /**
     * @var array
     */
    protected $pdoOptions = [];

    /**
     * @var string
     */
    protected $user = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'pgsql';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $buildSettings = $this->builder->getConfig('build_settings');

        if (!empty($buildSettings['pgsql']['host'])) {
            $this->host = $this->builder->interpolate($buildSettings['pgsql']['host']);
        }

        if (!empty($buildSettings['pgsql']['port'])) {
            $this->port = (int)$this->builder->interpolate($buildSettings['pgsql']['port']);
        }

        if (!empty($buildSettings['pgsql']['dbname'])) {
            $this->dbName = $this->builder->interpolate($buildSettings['pgsql']['dbname']);
        }

        if (!empty($buildSettings['pgsql']['options']) && \is_array($buildSettings['pgsql']['options'])) {
            $this->pdoOptions = $buildSettings['pgsql']['options'];
        }

        if (!empty($buildSettings['pgsql']['user'])) {
            $this->user = $this->builder->interpolate($buildSettings['pgsql']['user']);
        }

        if (array_key_exists('password', $buildSettings['pgsql'])) {
            $this->password = $this->builder->interpolate($buildSettings['pgsql']['password']);
        }

        if (!empty($this->options['queries']) && \is_array($this->options['queries'])) {
            $this->queries = $this->options['queries'];
        }
    }

    /**
     * Connects to PgSQL and runs a specified set of queries.
     * @return bool
     */
    public function execute()
    {
        try {
            $pdoOptions = array_merge([
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ], $this->pdoOptions);
            $dsn     = sprintf('pgsql:host=%s;port=%s', $this->host, $this->port);

            if (null !== $this->dbName) {
                $dsn .= ';dbname=' . $this->dbName;
            }

            $pdo = new PDO($dsn, $this->user, $this->password, $pdoOptions);

            foreach ($this->queries as $query) {
                $pdo->query($query);
            }
        } catch (Exception $ex) {
            $this->builder->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }
}
