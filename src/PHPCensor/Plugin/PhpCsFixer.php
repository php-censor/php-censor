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
    protected $directory  = null;
    protected $args       = '';

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

        if (isset($options['directory']) && $options['directory']) {
            $this->directory = $builder->interpolate($options['directory']);
        }
    }

    /**
     * Run PHP CS Fixer.
     *
     * @return bool
     */
    public function execute()
    {
        $cmd = '';
        if (!empty($this->directory)) {
            $cmd = 'cd ' . $this->directory . ' && ';
        }

        $phpCsFixer = $this->builder->findBinary('php-cs-fixer');
        $cmd        .= $phpCsFixer . ' fix . %s';
        $success    = $this->builder->executeCommand($cmd, $this->args);

        return $success;
    }
}
