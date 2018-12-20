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
     * @var string
     */

    protected $executable;
    /**
     * Warning : you can only set subdirectory of $directory
     *
     * @var string
     */
    protected $ignore;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_loc';
    }

    /**
     * {@inheritdoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        if (Build::STAGE_TEST == $stage) {
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

        $this->directory = $this->builder->directory;
        if (isset($options['directory']) && !empty($options['directory'])) {
            $this->directory = $this->getWorkingDirectory($options);
        } else {
            $this->directory = $this->builder->interpolate('%BUILD_PATH%' . $this->directory);
        }
        // only sub - directory of $this->directory can be ignored, and string must not include root
        if (array_key_exists('ignore', $options)) {
            $this->ignore = $this->ignorePathRelativeToDirectory($this->directory, array_merge($this->builder->ignore, $options['ignore']));
        } else {
            $this->ignore = $this->ignorePathRelativeToDirectory($this->directory, $this->builder->ignore);
        }
        $this->executable = $this->findBinary('phploc');
    }

    /**
     * Runs PHP LOC in a specified directory.
     */
    public function execute()
    {
        $ignore = '';
        if (is_array($this->ignore)) {
            $map = function ($item) {
                return ' --exclude ' . rtrim($item, '/');
            };

            $ignore = array_map($map, $this->ignore);
            $ignore = implode('', $ignore);
        }

        $phploc = $this->executable;

        $success = $this->builder->executeCommand($phploc . ' %s "%s"', $ignore, $this->directory);
        $output  = $this->builder->getLastOutput();

        if (preg_match_all('/\((LOC|CLOC|NCLOC|LLOC)\)\s+([0-9]+)/', $output, $matches)) {
            $data = [];
            foreach ($matches[1] as $k => $v) {
                $data[$v] = (int) $matches[2][$k];
            }

            $this->build->storeMeta((self::pluginName() . '-data'), $data);
        }

        return $success;
    }
}
