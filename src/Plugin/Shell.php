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
     * @var string[] $commands The commands to be executed
     */
    protected $commands = [];

    protected $executeAll = false;

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

        if (array_key_exists('execute_all', $options) && $options['execute_all']) {
            $this->executeAll = true;
        }

        if (isset($options['commands']) && is_array($options['commands'])) {
            $this->commands = $options['commands'];

            return;
        }

        /** @deprecated Option "command" is deprecated and will be deleted in version 2.0. Use the option "commands" instead. */
        if (isset($options['command'])) {
            $builder->logWarning(
                '[DEPRECATED] Option "command" is deprecated and will be deleted in version 2.0. Use the option "commands" instead.'
            );

            /** @deprecated Variable "%buildpath%" is deprecated and will be deleted in version 2.0. Use the interpolation variable "%BUILD_PATH%" instead. */
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
        /** @deprecated Commands list without option is deprecated and will be deleted in version 2.0. Use the option "commands" instead. */
        if (is_array($options)) {
            $builder->logWarning(
                '[DEPRECATED] Commands list without option is deprecated and will be deleted in version 2.0. Use the option "commands" instead.'
            );

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
        $result = true;
        foreach ($this->commands as $command) {
            $command = $this->builder->interpolate($command);

            if (!$this->builder->executeCommand($command)) {
                $result = false;

                if (!$this->executeAll) {
                    return $result;
                }
            }
        }

        return $result;
    }
}
