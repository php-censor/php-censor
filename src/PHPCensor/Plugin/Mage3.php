<?php
/**
 * PHPCensor - Continuous Integration for PHP
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use Psr\Log\LogLevel;
use \PHPCensor\Plugin;

/**
 * Integrates PHPCensor with Mage v3: https://github.com/andres-montanez/Magallanes
 * @package      PHPCensor
 * @subpackage   Plugins
 */
class Mage3 extends Plugin
{
    protected $mage_bin = 'mage';
    protected $mage_env;
    protected $mage_log_dir;

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

        $config = $builder->getSystemConfig('mage3');
        if (!empty($config['bin'])) {
            $this->mage_bin = $config['bin'];
        }

        if (isset($options['env'])) {
            $this->mage_env = $builder->interpolate($options['env']);
        }

        if (isset($options['log_dir'])) {
            $this->mage_log_dir = $builder->interpolate($options['log_dir']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (empty($this->mage_env)) {
            $this->builder->logFailure('You must specify environment.');
            return false;
        }

        $result = $this->builder->executeCommand($this->mage_bin . ' -n deploy ' . $this->mage_env);

        try {
            $this->builder->log('########## MAGE LOG BEGIN ##########');
            $this->builder->log($this->getMageLog());
            $this->builder->log('########## MAGE LOG END ##########');
        } catch (\Exception $e) {
            $this->builder->log($e->getMessage(), LogLevel::NOTICE);
        }

        return $result;
    }

    /**
     * Get mage log lines
     * @return array
     * @throws \Exception
     */
    protected function getMageLog()
    {
        $logs_dir = $this->build->getBuildPath() . (!empty($this->mage_log_dir) ? '/' . $this->mage_log_dir : '');
        if (!is_dir($logs_dir)) {
            throw new \Exception('Log directory not found');
        }

        $list = scandir($logs_dir);
        if ($list === false) {
            throw new \Exception('Log dir read fail');
        }

        $list = array_filter($list, function ($name) {
            return preg_match('/^\d+_\d+\.log$/', $name);
        });
        if (empty($list)) {
            throw new \Exception('Log dir filter fail');
        }

        $res = sort($list);
        if ($res === false) {
            throw new \Exception('Logs sort fail');
        }

        $last_log_file = end($list);
        if ($last_log_file === false) {
            throw new \Exception('Get last Log name fail');
        }

        $log_content = file_get_contents($logs_dir . '/' . $last_log_file);
        if ($log_content === false) {
            throw new \Exception('Get last Log content fail');
        }

        $lines = explode("\n", $log_content);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return $lines;
    }
}
