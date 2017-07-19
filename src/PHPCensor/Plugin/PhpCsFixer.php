<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * PHP CS Fixer - Works with the PHP Coding Standards Fixer for testing coding standards.
 * 
 * @author Gabriel Baker <gabriel@autonomicpilot.co.uk>
 */
class PhpCsFixer extends Plugin
{
    protected $directory = null;
    protected $args      = '';
    
    protected $config    = false;
    protected $configs   = [
        '.php_cs',
        '.php_cs.dist',
    ];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_cs_fixer';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (!empty($options['args'])) {
            $this->args = $options['args'];
        }

        if (isset($options['verbose']) && $options['verbose']) {
            $this->args .= ' --verbose';
        }

        if (isset($options['diff']) && $options['diff']) {
            $this->args .= ' --diff';
        }

        if (isset($options['rules']) && $options['rules']) {
            $this->args .= ' --rules=' . $options['rules'];
        }

        if (isset($options['config']) && $options['config']) {
            $this->config = true;
            $this->args   .= ' --config=' . $builder->interpolate($options['config']);
        }

        if (isset($options['directory']) && $options['directory']) {
            $this->directory = $builder->interpolate($options['directory']);
        }
    }

    /**
     * Run PHP CS Fixer.
     *
     * @return boolean
     */
    public function execute()
    {
        $directory = '';
        if (!empty($this->directory)) {
            $directory = $this->directory;
        }

        if (!$this->config) {
            foreach ($this->configs as $config) {
                if (file_exists($this->builder->buildPath . '/' . $config)) {
                    $this->config = true;
                    $this->args   .= ' --config=./' . $config;
                    break;
                }
            }
        }

        if (!$this->config && !$directory) {
            $directory = '.';
        }

        $phpCsFixer = $this->findBinary('php-cs-fixer');
        $cmd        = $phpCsFixer . ' fix ' . $directory . ' %s';
        $success    = $this->builder->executeCommand($cmd, $this->args);

        return $success;
    }
}
