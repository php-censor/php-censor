<?php

namespace PHPCensor\Plugin;

use PDO;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use b8\Database;

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

        $config = Database::getConnection('write')->getDetails();

        $this->host =(defined('DB_HOST')) ? DB_HOST : null;
        $this->user = $config['user'];
        $this->pass = $config['pass'];

        $buildSettings = $this->builder->getConfig('build_settings');

        if (!isset($buildSettings['mysql'])) {
            return;
        }

        if (!empty($buildSettings['mysql']['host'])) {
            $this->host = $this->builder->interpolate($buildSettings['mysql']['host']);
        }

        if (!empty($buildSettings['mysql']['user'])) {
            $this->user = $this->builder->interpolate($buildSettings['mysql']['user']);
        }

        if (array_key_exists('pass', $buildSettings['mysql'])) {
            $this->pass = $buildSettings['mysql']['pass'];
        }
    }

    /**
    * Connects to MySQL and runs a specified set of queries.
    * @return boolean
    */
    public function execute()
    {
        try {
            $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $pdo  = new PDO('mysql:host=' . $this->host, $this->user, $this->pass, $opts);

            foreach ($this->options as $query) {
                if (!is_array($query)) {
                    // Simple query
                    $pdo->query($this->builder->interpolate($query));
                } elseif (isset($query['import'])) {
                    // SQL file execution
                    $this->executeFile($query['import']);
                } else {
                    throw new \Exception('Invalid command.');
                }
            }
        } catch (\Exception $ex) {
            $this->builder->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param string $query
     * @return boolean
     * @throws \Exception
     */
    protected function executeFile($query)
    {
        if (!isset($query['file'])) {
            throw new \Exception('Import statement must contain a \'file\' key');
        }

        $import_file = $this->builder->buildPath . $this->builder->interpolate($query['file']);
        if (!is_readable($import_file)) {
            throw new \Exception(sprintf('Cannot open SQL import file: %s', $import_file));
        }

        $database = isset($query['database']) ? $this->builder->interpolate($query['database']) : null;

        $import_command = $this->getImportCommand($import_file, $database);
        if (!$this->builder->executeCommand($import_command)) {
            throw new \Exception('Unable to execute SQL file');
        }

        return true;
    }

    /**
     * Builds the MySQL import command required to import/execute the specified file
     *
     * @param string $import_file Path to file, relative to the build root
     * @param string $database If specified, this database is selected before execution
     *
     * @return string
     */
    protected function getImportCommand($import_file, $database = null)
    {
        $decompression = [
            'bz2' => '| bzip2 --decompress',
            'gz'  => '| gzip --decompress',
        ];

        $extension = strtolower(pathinfo($import_file, PATHINFO_EXTENSION));
        $decomp_cmd = '';
        if (array_key_exists($extension, $decompression)) {
            $decomp_cmd = $decompression[$extension];
        }

        $args = [
            ':import_file' => escapeshellarg($import_file),
            ':decomp_cmd'  => $decomp_cmd,
            ':host'        => escapeshellarg($this->host),
            ':user'        => escapeshellarg($this->user),
            ':pass'        => (!$this->pass) ? '' : '-p' . escapeshellarg($this->pass),
            ':database'    => ($database === null)? '': escapeshellarg($database),
        ];

        return strtr('cat :import_file :decomp_cmd | mysql -h:host -u:user :pass :database', $args);
    }
}
