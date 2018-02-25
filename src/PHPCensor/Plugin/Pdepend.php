<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Pdepend Plugin - Allows Pdepend report
 *
 * @author Johan van der Heide <info@japaveh.nl>
 */
class Pdepend extends Plugin
{
    protected $args;

    /**
     * @var string
     */
    protected $buildDirectory;

    /**
     * @var string
     */
    protected $buildBranchDirectory;

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
     * @var string
     */
    protected $buildLocation;

    /**
     * @var string
     */
    protected $buildBranchLocation;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'pdepend';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory = isset($options['directory'])
            ? $options['directory']
            : $this->builder->buildPath;

        $this->summary = 'summary.xml';
        $this->pyramid = 'pyramid.svg';
        $this->chart   = 'chart.svg';

        $this->buildDirectory       = $build->getBuildDirectory();
        $this->buildBranchDirectory = $build->getBuildBranchDirectory();

        $this->buildLocation       = PUBLIC_DIR . 'artifacts/pdepend/' . $this->buildDirectory;
        $this->buildBranchLocation = PUBLIC_DIR . 'artifacts/pdepend/' . $this->buildBranchDirectory;
    }

    /**
     * Runs Pdepend with the given criteria as arguments
     */
    public function execute()
    {
        if (!file_exists($this->buildLocation)) {
            mkdir($this->buildLocation, (0777 & ~umask()), true);
        }
        if (!is_writable($this->buildLocation)) {
            throw new \Exception(sprintf('The location %s is not writable or does not exist.', $this->buildLocation));
        }

        $pdepend = $this->findBinary('pdepend');

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
            $this->buildLocation . DIRECTORY_SEPARATOR . $this->summary,
            $this->buildLocation . DIRECTORY_SEPARATOR . $this->chart,
            $this->buildLocation . DIRECTORY_SEPARATOR . $this->pyramid,
            $ignore,
            $this->directory
        );

        $fileSystem = new Filesystem();

        if (file_exists($this->buildLocation)) {
            $fileSystem->remove($this->buildBranchLocation);
            $fileSystem->mirror($this->buildLocation, $this->buildBranchLocation);
        }

        $config = $this->builder->getSystemConfig('php-censor');

        if ($success) {
            $this->builder->logSuccess(
                sprintf(
                    "\nPdepend successful build report.\nYou can use report for this build for inclusion in the readme.md file:\n%s,\n![Chart](%s \"Pdepend Chart\") and\n![Pyramid](%s \"Pdepend Pyramid\")\n\nOr report for last build in the branch:\n%s,\n![Chart](%s \"Pdepend Chart\") and\n![Pyramid](%s \"Pdepend Pyramid\")\n",
                    $config['url'] . '/artifacts/pdepend/' . $this->buildDirectory . '/' . $this->summary,
                    $config['url'] . '/artifacts/pdepend/' . $this->buildDirectory . '/' . $this->chart,
                    $config['url'] . '/artifacts/pdepend/' . $this->buildDirectory . '/' . $this->pyramid,
                    $config['url'] . '/artifacts/pdepend/' . $this->buildBranchDirectory . '/' . $this->summary,
                    $config['url'] . '/artifacts/pdepend/' . $this->buildBranchDirectory . '/' . $this->chart,
                    $config['url'] . '/artifacts/pdepend/' . $this->buildBranchDirectory . '/' . $this->pyramid
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
            if (file_exists($this->buildLocation . DIRECTORY_SEPARATOR . $file)) {
                unlink($this->buildLocation . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
