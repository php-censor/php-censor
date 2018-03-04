<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Loc - Allows PHP Copy / Lines of Code testing.
 * 
 * @author Johan van der Heide <info@japaveh.nl>
 */
class PhpLoc extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_loc';
    }
    
    /**
     * Check if this plugin can be executed.
     * @param $stage
     * @param Builder $builder
     * @param Build $build
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == Build::STAGE_TEST) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory = $this->builder->buildPath;

        if (isset($options['directory'])) {
            $this->directory .= $options['directory'];
        }
    }

    /**
     * Runs PHP Copy/Paste Detector in a specified directory.
     */
    public function execute()
    {
        $ignore = '';

        if (count($this->builder->ignore)) {
            $map = function ($item) {
                return ' --exclude ' . rtrim($item, DIRECTORY_SEPARATOR);
            };

            $ignore = array_map($map, $this->builder->ignore);
            $ignore = implode('', $ignore);
        }

        $phploc = $this->findBinary('phploc');

        $success = $this->builder->executeCommand($phploc . ' %s "%s"', $ignore, $this->directory);
        $output  = $this->builder->getLastOutput();

        if (preg_match_all('/\((LOC|CLOC|NCLOC|LLOC)\)\s+([0-9]+)/', $output, $matches)) {
            $data = [];
            foreach ($matches[1] as $k => $v) {
                $data[$v] = (int)$matches[2][$k];
            }

            $this->build->storeMeta('phploc', $data);
        }

        return $success;
    }
}
