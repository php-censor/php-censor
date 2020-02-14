<?php
/**
 * PHPCensor - Continuous Integration for PHP
 */

namespace PHPCensor\Plugin;

use Exception;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Integrates PHPCensor with Mage v3: https://github.com/andres-montanez/Magallanes
 *
 * @package      PHPCensor
 * @subpackage   Plugins
 */
class Mage3 extends Plugin
{
    protected $mageEnv;
    protected $mageLogDir;

    /**
     * {@inheritdoc}
     */
    public static function pluginName()
    {
        return 'mage3';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->builder->findBinary(['mage', 'mage.phar']);

        if (isset($options['env'])) {
            $this->mageEnv = $builder->interpolate($options['env']);
        }

        if (isset($options['log_dir'])) {
            $this->mageLogDir = $builder->interpolate($options['log_dir']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (empty($this->mageEnv)) {
            $this->builder->logFailure('You must specify environment.');
            return false;
        }

        $result = $this->builder->executeCommand($this->executable . ' -n deploy ' . $this->mageEnv);

        try {
            $this->builder->log('########## MAGE LOG BEGIN ##########');
            $this->builder->log($this->getMageLog());
            $this->builder->log('########## MAGE LOG END ##########');
        } catch (Exception $e) {
            $this->builder->logFailure($e->getMessage());
        }

        return $result;
    }

    /**
     * Get mage log lines
     * @return array
     * @throws Exception
     */
    protected function getMageLog()
    {
        $logsDir = $this->build->getBuildPath() . (!empty($this->mageLogDir) ? '/' . $this->mageLogDir : '');
        if (!is_dir($logsDir)) {
            throw new Exception('Log directory not found');
        }

        $list = scandir($logsDir);
        if ($list === false) {
            throw new Exception('Log dir read fail');
        }

        $list = array_filter($list, function ($name) {
            return preg_match('/^\d+_\d+\.log$/', $name);
        });
        if (empty($list)) {
            throw new Exception('Log dir filter fail');
        }

        $res = sort($list);
        if ($res === false) {
            throw new Exception('Logs sort fail');
        }

        $lastLogFile = end($list);
        if ($lastLogFile === false) {
            throw new Exception('Get last Log name fail');
        }

        $logContent = file_get_contents($logsDir . '/' . $lastLogFile);
        if ($logContent === false) {
            throw new Exception('Get last Log content fail');
        }

        $lines = explode("\n", $logContent);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return $lines;
    }
}
