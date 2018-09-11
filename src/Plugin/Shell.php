<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Shell Plugin - Allows execute shell commands.
 * 
 * @author Kinn Coelho Julião <kinncj@gmail.com>
 */
class Shell extends Plugin
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var string[] $commands The commands to be executed
     */
    protected $commands = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'shell';
    }
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (isset($options['command'])) {
            // Keeping this for backwards compatibility, new projects should use interpolation vars.
            $options['command'] = str_replace("%buildpath%", $this->builder->buildPath, $options['command']);
            $this->commands = [$options['command']];
            return;
        }

        /*
         * Support the new syntax:
         *
         * shell:
         *     - "cd /www"
         *     - "rm -f file.txt"
         */
        if (is_array($options)) {
            $this->commands = $options;
        }
    }

    /**
     * Runs the shell command.
     *
     * @return bool
     */
    public function execute()
    {
        foreach ($this->commands as $command) {
            $command = $this->builder->interpolate($command);

            if (!$this->builder->executeCommand($command)) {
                return false;
            }
        }

        return true;
    }
}
