<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * Composer Plugin - Provides access to Composer functionality.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Composer extends Plugin implements ZeroConfigPluginInterface
{
    protected $directory;
    protected $action;
    protected $preferDist;
    protected $noDev;
    protected $ignorePlatformReqs;
    protected $preferSource;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'composer';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->action             = 'install';
        $this->preferDist         = false;
        $this->preferSource       = false;
        $this->noDev              = false;
        $this->ignorePlatformReqs = false;
        $this->directory          = $this->getWorkingDirectory($options);

        if (array_key_exists('action', $options)) {
            $this->action = $options['action'];
        }

        if (array_key_exists('prefer_dist', $options)) {
            $this->preferDist = (bool)$options['prefer_dist'];
        }

        if (array_key_exists('prefer_source', $options)) {
            $this->preferDist   = false;
            $this->preferSource = (bool)$options['prefer_source'];
        }

        if (array_key_exists('no_dev', $options)) {
            $this->noDev = (bool)$options['no_dev'];
        }

        if (array_key_exists('ignore_platform_reqs', $options)) {
            $this->ignorePlatformReqs = (bool)$options['ignore_platform_reqs'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        $path = $build->getBuildPath() . '/composer.json';

        if (file_exists($path) && $stage == Build::STAGE_SETUP) {
            return true;
        }

        return false;
    }

    /**
    * Executes Composer and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $composerLocation = $this->findBinary(['composer', 'composer.phar']);
        $cmd              = $composerLocation . ' --no-ansi --no-interaction ';

        if ($this->preferDist) {
            $this->builder->log('Using --prefer-dist flag');
            $cmd .= ' --prefer-dist';
        }

        if ($this->preferSource) {
            $this->builder->log('Using --prefer-source flag');
            $cmd .= ' --prefer-source';
        }

        if ($this->noDev) {
            $this->builder->log('Using --no-dev flag');
            $cmd .= ' --no-dev';
        }

        if ($this->ignorePlatformReqs) {
            $this->builder->log('Using --ignore-platform-reqs flag');
            $cmd .= ' --ignore-platform-reqs';
        }

        $cmd .= ' --working-dir="%s" %s';

        return $this->builder->executeCommand($cmd, $this->directory, $this->action);
    }
}
