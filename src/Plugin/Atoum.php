<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Atoum plugin, runs Atoum tests within a project.
 */
class Atoum extends Plugin
{
    /**
     * @var string
     */
    protected $args;

    /**
     * @var string
     */
    protected $config;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'atoum';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->findBinary(['atoum', 'atoum.phar']);

        if (isset($options['args'])) {
            $this->args = $options['args'];
        }

        if (isset($options['config'])) {
            $this->config = $options['config'];
        }
    }

    /**
     * Run the Atoum plugin.
     *
     * @return bool
     */
    public function execute()
    {
        $cmd = $this->executable;

        if (null !== $this->args) {
            $cmd .= " {$this->args}";
        }

        if (null !== $this->config) {
            $cmd .= " -c '{$this->config}'";
        }

        $cmd .= " --directories '{$this->directory}'";

        $status = true;

        $this->builder->executeCommand($cmd);

        $output = $this->builder->getLastOutput();

        if (false === \strpos($output, "Success (")) {
            $status = false;
            $this->builder->log($output);
        }

        if (!$output) {
            $status = false;
            $this->builder->log('No tests have been performed.');
        }

        return $status;
    }
}
