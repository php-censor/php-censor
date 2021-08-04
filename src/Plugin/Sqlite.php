<?php

namespace PHPCensor\Plugin;

use Exception;
use PDO;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * SQLite Plugin — Provides access to a SQLite database.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
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
    protected $path = '';

    /**
     * @var array
     */
    protected $pdoOptions = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'sqlite';
    }

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

        if (!empty($buildSettings['sqlite']['path'])) {
            $this->path = $buildSettings['sqlite']['path'];
        }

        if (!empty($buildSettings['sqlite']['options']) && \is_array($buildSettings['sqlite']['options'])) {
            $this->pdoOptions = $buildSettings['sqlite']['options'];
        }

        if (!empty($this->options['queries']) && \is_array($this->options['queries'])) {
            foreach ($this->options['queries'] as $query) {
                $this->queries[] = $this->builder->interpolate($query);
            }
        }
    }

    /**
     * Connects to SQLite and runs a specified set of queries.
     * @return bool
     */
    public function execute()
    {
        try {
            $pdoOptions = \array_merge([
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ], $this->pdoOptions);

            $pdo  = new PDO(('sqlite:' . $this->path), null, null, $pdoOptions);

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
