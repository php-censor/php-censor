<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Pdepend Plugin - Allows Pdepend report
 * 
 * @author       Johan van der Heide <info@japaveh.nl>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Pdepend extends Plugin
{
    protected $args;

    /**
     * @var string Directory which needs to be scanned
     */
    protected $directory;

    /**
     * @var string File where the summary.xml is stored
     */
    protected $summary;

    /**
     * @var string File where the chart.svg is stored
     */
    protected $chart;

    /**
     * @var string File where the pyramid.svg is stored
     */
    protected $pyramid;

    /**
     * @var string Location on the server where the files are stored. Preferably in the webroot for inclusion
     *             in the readme.md of the repository
     */
    protected $location;

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory = isset($options['directory']) ? $options['directory'] : $this->builder->buildPath;

        $title          = $this->builder->getBuildProjectTitle();
        $this->summary  = $title . '-summary.xml';
        $this->pyramid  = $title . '-pyramid.svg';
        $this->chart    = $title . '-chart.svg';
        $this->location = $this->builder->buildPath . '..' . DIRECTORY_SEPARATOR . 'pdepend';
    }

    /**
     * Runs Pdepend with the given criteria as arguments
     */
    public function execute()
    {
        if (!file_exists($this->location)) {
            mkdir($this->location);
        }
        if (!is_writable($this->location)) {
            throw new \Exception(sprintf('The location %s is not writable or does not exist.', $this->location));
        }

        $pdepend = $this->builder->findBinary('pdepend');

        $cmd = $pdepend . ' --summary-xml="%s" --jdepend-chart="%s" --overview-pyramid="%s" %s "%s"';

        $this->removeBuildArtifacts();

        // If we need to ignore directories
        if (count($this->builder->ignore)) {
            $ignore = ' --ignore=' . implode(',', $this->builder->ignore);
        } else {
            $ignore = '';
        }

        $success = $this->builder->executeCommand(
            $cmd,
            $this->location . DIRECTORY_SEPARATOR . $this->summary,
            $this->location . DIRECTORY_SEPARATOR . $this->chart,
            $this->location . DIRECTORY_SEPARATOR . $this->pyramid,
            $ignore,
            $this->directory
        );

        $config = $this->builder->getSystemConfig('php-censor');

        if ($success) {
            $this->builder->logSuccess(
                sprintf(
                    "Pdepend successful. You can use %s\n, ![Chart](%s \"Pdepend Chart\")\n
                    and ![Pyramid](%s \"Pdepend Pyramid\")\n
                    for inclusion in the readme.md file",
                    $config['url'] . '/build/pdepend/' . $this->summary,
                    $config['url'] . '/build/pdepend/' . $this->chart,
                    $config['url'] . '/build/pdepend/' . $this->pyramid
                )
            );
        }

        return $success;
    }

    /**
     * Remove files created from previous builds
     */
    protected function removeBuildArtifacts()
    {
        //Remove the created files first
        foreach ([$this->summary, $this->chart, $this->pyramid] as $file) {
            if (file_exists($this->location . DIRECTORY_SEPARATOR . $file)) {
                unlink($this->location . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
