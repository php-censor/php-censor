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
    protected $executable;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $directory;

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

        if (isset($options['executable'])) {
            $this->executable = $this->builder->buildPath . DIRECTORY_SEPARATOR.$options['executable'];
        } else {
            $this->executable = $this->findBinary('atoum');
        }

        if (isset($options['args'])) {
            $this->args = $options['args'];
        }

        if (isset($options['config'])) {
            $this->config = $options['config'];
        }

        if (isset($options['directory'])) {
            $this->directory = $options['directory'];
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

        if ($this->args !== null) {
            $cmd .= " {$this->args}";
        }

        if ($this->config !== null) {
            $cmd .= " -c '{$this->config}'";
        }

        if ($this->directory !== null) {
            $dirPath = $this->builder->buildPath . DIRECTORY_SEPARATOR . $this->directory;
            $cmd .= " -d '{$dirPath}'";
        }

        chdir($this->builder->buildPath);

        $output = '';
        $status = true;

        exec($cmd, $output);

        if (count(preg_grep("/Success \(/", $output)) == 0) {
            $status = false;
            $this->builder->log($output);
        }

        if (count($output) == 0) {
            $status = false;
            $this->builder->log('No tests have been performed.');
        }

        return $status;
    }
}
