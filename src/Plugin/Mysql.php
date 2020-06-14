<?php

namespace PHPCensor\Plugin;

use Exception;
use PDO;
use PHPCensor\Builder;
use PHPCensor\Database;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * MySQL Plugin - Provides access to a MySQL database.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Steve Kamerman <stevekamerman@gmail.com>
 */
class Mysql extends Plugin
{
    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 3306;

    /**
     * @var string|null
     */
    protected $dbName = null;

    /**
     * @var string|null
     */
    protected $charset = null;

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
     * @var array
     */
    protected $imports = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'mysql';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $buildSettings = $this->builder->getConfig('build_settings');
        if (!isset($buildSettings['mysql'])) {
            return;
        }

        if (!empty($buildSettings['mysql']['host'])) {
            $this->host = $this->builder->interpolate($buildSettings['mysql']['host']);
        }

        if (!empty($buildSettings['mysql']['port'])) {
            $this->port = (int)$this->builder->interpolate($buildSettings['mysql']['port']);
        }

        if (!empty($buildSettings['mysql']['dbname'])) {
            $this->dbName = $this->builder->interpolate($buildSettings['mysql']['dbname']);
        }

        if (!empty($buildSettings['mysql']['charset'])) {
            $this->charset = $this->builder->interpolate($buildSettings['mysql']['charset']);
        }

        if (!empty($buildSettings['mysql']['options']) && \is_array($buildSettings['mysql']['options'])) {
            $this->pdoOptions = $buildSettings['mysql']['options'];
        }

        if (!empty($buildSettings['mysql']['user'])) {
            $this->user = $this->builder->interpolate($buildSettings['mysql']['user']);
        }

        if (\array_key_exists('password', $buildSettings['mysql'])) {
            $this->password = $this->builder->interpolate($buildSettings['mysql']['password']);
        }

        if (!empty($this->options['queries']) && \is_array($this->options['queries'])) {
            $this->queries = $this->options['queries'];
        }

        if (!empty($this->options['imports']) && \is_array($this->options['imports'])) {
            $this->imports = $this->options['imports'];
        }
    }

    /**
    * Connects to MySQL and runs a specified set of queries.
    * @return bool
    */
    public function execute()
    {
        try {
            $pdoOptions = \array_merge([
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ], $this->pdoOptions);
            $dsn     = \sprintf('mysql:host=%s;port=%s', $this->host, $this->port);

            if (null !== $this->dbName) {
                $dsn .= ';dbname=' . $this->dbName;
            }

            if (null !== $this->charset) {
                $dsn .= ';charset=' . $this->charset;
            }

            $pdo = new PDO($dsn, $this->user, $this->password, $pdoOptions);

            foreach ($this->queries as $query) {
                $pdo->query($query);
            }

            foreach ($this->imports as $import) {
                $this->executeFile($import);
            }
        } catch (Exception $ex) {
            $this->builder->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param array $query
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function executeFile(array $query)
    {
        if (!isset($query['file'])) {
            throw new Exception('Import statement must contain a \'file\' key');
        }

        $importFile = $this->builder->buildPath . $this->builder->interpolate($query['file']);
        if (!is_readable($importFile)) {
            throw new Exception(sprintf('Cannot open SQL import file: %s', $importFile));
        }

        $database = isset($query['database']) ? $this->builder->interpolate($query['database']) : null;

        $importCommand = $this->getImportCommand($importFile, $database);
        if (!$this->builder->executeCommand($importCommand)) {
            throw new Exception('Unable to execute SQL file');
        }

        return true;
    }

    /**
     * Builds the MySQL import command required to import/execute the specified file
     *
     * @param string $importFile Path to file, relative to the build root
     * @param string $database   If specified, this database is selected before execution
     *
     * @return string
     */
    protected function getImportCommand($importFile, $database = null)
    {
        $decompression = [
            'bz2' => '| bzip2 --decompress',
            'gz'  => '| gzip --decompress',
        ];

        $extension        = strtolower(pathinfo($importFile, PATHINFO_EXTENSION));
        $decompressionCmd = '';
        if (array_key_exists($extension, $decompression)) {
            $decompressionCmd = $decompression[$extension];
        }

        $args = [
            ':import_file' => escapeshellarg($importFile),
            ':decomp_cmd'  => $decompressionCmd,
            ':host'        => escapeshellarg($this->host),
            ':user'        => escapeshellarg($this->user),
            ':pass'        => (!$this->password) ? '' : '-p' . escapeshellarg($this->password),
            ':database'    => ($database === null)? '': escapeshellarg($database),
        ];

        return strtr('cat :import_file :decomp_cmd | mysql -h:host -u:user :pass :database', $args);
    }
}
