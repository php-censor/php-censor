<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Git plugin.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Git extends Plugin
{
    protected $actions = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'git';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->actions = $options;
    }

    /**
     * Run the Git plugin.
     * @return bool
     */
    public function execute()
    {
        // Check if there are any actions to be run for the branch we're running on:
        if (!\array_key_exists($this->build->getBranch(), $this->actions)) {
            return true;
        }

        $success = true;
        foreach ($this->actions[$this->build->getBranch()] as $action => $options) {
            if (!$this->runAction($action, $options)) {
                $success = false;

                break;
            }
        }

        return $success;
    }

    /**
     * Determine which action to run, and run it.
     *
     * @param $action
     *
     * @return bool
     */
    protected function runAction($action, array $options = [])
    {
        switch ($action) {
            case 'merge':
                return $this->runMergeAction($options);

            case 'tag':
                return $this->runTagAction($options);

            case 'pull':
                return $this->runPullAction($options);

            case 'push':
                return $this->runPushAction($options);
        }


        return false;
    }

    /**
     * Handle a merge action.
     * @param $options
     * @return bool
     */
    protected function runMergeAction($options)
    {
        if (\array_key_exists('branch', $options)) {
            $cmd = 'cd "%s" && git checkout %s && git merge "%s"';
            $path = $this->builder->buildPath;

            return $this->builder->executeCommand($cmd, $path, $options['branch'], $this->build->getBranch());
        }
    }

    /**
     * Handle a tag action.
     * @param $options
     * @return bool
     */
    protected function runTagAction($options)
    {
        $tagName = \date('Ymd-His');
        $message = \sprintf('Tag created by PHP Censor: %s', \date('Y-m-d H:i:s'));

        if (\array_key_exists('name', $options)) {
            $tagName = $this->builder->interpolate($options['name'], true);
        }

        if (\array_key_exists('message', $options)) {
            $message = $this->builder->interpolate($options['message'], true);
        }

        $cmd = 'git tag %s -m "%s"';

        return $this->builder->executeCommand($cmd, $tagName, $message);
    }

    /**
     * Handle a pull action.
     * @param $options
     * @return bool
     */
    protected function runPullAction($options)
    {
        $branch = $this->build->getBranch();
        $remote = 'origin';

        if (\array_key_exists('branch', $options)) {
            $branch = $this->builder->interpolate($options['branch'], true);
        }

        if (\array_key_exists('remote', $options)) {
            $remote = $this->builder->interpolate($options['remote'], true);
        }

        return $this->builder->executeCommand('git pull %s %s', $remote, $branch);
    }

    /**
     * Handle a push action.
     * @param $options
     * @return bool
     */
    protected function runPushAction($options)
    {
        $branch = $this->build->getBranch();
        $remote = 'origin';

        if (\array_key_exists('branch', $options)) {
            $branch = $this->builder->interpolate($options['branch'], true);
        }

        if (\array_key_exists('remote', $options)) {
            $remote = $this->builder->interpolate($options['remote'], true);
        }

        return $this->builder->executeCommand('git push %s %s', $remote, $branch);
    }
}
