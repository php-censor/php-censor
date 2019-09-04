<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;

/**
 * Deployer plugin for PHPCensor: http://deployer.org
 *
 * @author Alexey Boyko <ket4yiit@gmail.com>
 */
class DeployerOrg extends \PHPCensor\Plugin
{
    protected $branch;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'deployer_org';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->builder->findBinary(['dep', 'dep.phar']);
        $this->branch     = $this->build->getBranch();
    }

    /**
     * PHPCensor plugin executor.
     *
     * @return bool Did plugin execute successfully
     */
    public function execute()
    {
        if (($validationResult = $this->validateConfig()) !== null) {
            $this->builder->log($validationResult['message']);

            return $validationResult['successful'];
        }

        $branchConfig = $this->options[$this->branch];
        $options      = $this->getOptions($branchConfig);
        $deployerCmd  = "$this->executable $options";

        return $this->builder->executeCommand($deployerCmd);
    }

    /**
     * Validate config.
     *
     * $validationRes['message'] Message to log
     * $validationRes['successful'] Plugin status that is connected with error
     *
     *  @return array validation result
     */
    protected function validateConfig()
    {
        if (empty($this->options)) {
            return [
                'message'    => 'Can\'t find configuration for plugin!',
                'successful' => false
            ];
        }

        if (empty($this->options[$this->branch])) {
            return [
                'message'    => 'There is no specified config for this branch.',
                'successful' => true
            ];
        }

        $branchConf = $this->options[$this->branch];
        if (empty($branchConf['stage'])) {
            return [
                'message'    => 'There is no stage for this branch',
                'successful' => false
            ];
        }

        return null;
    }

    /**
     * Get verbosity flag.
     *
     * @param string $verbosity User defined verbosity level
     *
     * @return string Verbosity flag
     */
    protected function getVerbosityOption($verbosity)
    {
        $logLevelList = [
            'verbose'      =>'v',
            'very verbose' => 'vv',
            'debug'        => 'vvv',
            'quiet'        => 'q'
        ];

        $verbosity = strtolower(trim($verbosity));
        if ($verbosity !== 'normal') {
            return '-' . $logLevelList[$verbosity];
        } else {
            return '';
        }
    }

    /**
     * Make deployer options from config
     *
     * @param array $config Deployer configuration array
     *
     * @return string Deployer options
     */
    protected function getOptions($config)
    {
        $options = [];
        if (!empty($config['task'])) {
            $options[] = $config['task'];
        } else {
            $options[] = 'deploy';
        }

        if (!empty($config['stage'])) {
            $options[] = $config['stage'];
        }

        if (!empty($config['verbosity'])) {
            $options[] = $this->getVerbosityOption($config['verbosity']);
        }

        if (!empty($config['file'])) {
            $options[] = '--file=' . $config['file'];
        }

        return implode(' ', $options);
    }
}
