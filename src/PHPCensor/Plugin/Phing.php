<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Phing Plugin - Provides access to Phing functionality.
 *
 * @author Pavel Pavlov <ppavlov@alera.ru>
 */
class Phing extends Plugin
{
    protected $directory;
    protected $buildFile  = 'build.xml';
    protected $targets    = ['build'];
    protected $properties = [];
    protected $propertyFile;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'phing';
    }
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        /*
         * Set working directory
         */
        if (isset($options['directory'])) {
            $directory = $this->builder->buildPath . DIRECTORY_SEPARATOR . $options['directory'];
        } else {
            $directory = $this->builder->buildPath;
        }

        $this->setDirectory($directory);

        /*
         * Sen name of a non default build file
         */
        if (isset($options['build_file'])) {
            $this->setBuildFile($options['build_file']);
        }

        if (isset($options['targets'])) {
            $this->setTargets($options['targets']);
        }

        if (isset($options['properties'])) {
            $this->setProperties($options['properties']);
        }

        if (isset($options['property_file'])) {
            $this->setPropertyFile($options['property_file']);
        }
    }

    /**
     * Executes Phing and runs a specified targets
     */
    public function execute()
    {
        $phingExecutable = $this->findBinary('phing');

        $cmd[] = $phingExecutable . ' -f ' . $this->getBuildFilePath();

        if ($this->getPropertyFile()) {
            $cmd[] = '-propertyfile ' . $this->getPropertyFile();
        }

        $cmd[] = $this->propertiesToString();

        $cmd[] = '-logger phing.listener.DefaultLogger';
        $cmd[] = $this->targetsToString();
        $cmd[] = '2>&1';

        return $this->builder->executeCommand(implode(' ', $cmd), $this->directory, $this->targets);
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     *
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Converts an array of targets into a string.
     * @return string
     */
    private function targetsToString()
    {
        return implode(' ', $this->targets);
    }

    /**
     * @param array|string $targets
     *
     * @return $this
     */
    public function setTargets($targets)
    {
        if (is_string($targets)) {
            $targets = [$targets];
        }

        $this->targets = $targets;
    }

    /**
     * @return string
     */
    public function getBuildFile()
    {
        return $this->buildFile;
    }

    /**
     * @param mixed $buildFile
     *
     * @return $this
     * @throws \Exception
     */
    public function setBuildFile($buildFile)
    {
        if (!file_exists($this->getDirectory() . $buildFile)) {
            throw new \Exception('Specified build file does not exist.');
        }

        $this->buildFile = $buildFile;
    }

    /**
     * Get phing build file path.
     * @return string
     */
    public function getBuildFilePath()
    {
        return $this->getDirectory() . $this->buildFile;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function propertiesToString()
    {
        /**
         * fix the problem when execute phing out of the build dir
         * @ticket 748
         */
        if (!isset($this->properties['project.basedir'])) {
            $this->properties['project.basedir'] = $this->getDirectory();
        }

        $propertiesString = [];

        foreach ($this->properties as $name => $value) {
            $propertiesString[] = '-D' . $name . '="' . $value . '"';
        }

        return implode(' ', $propertiesString);
    }

    /**
     * @param array|string $properties
     *
     * @return $this
     */
    public function setProperties($properties)
    {
        if (is_string($properties)) {
            $properties = [$properties];
        }

        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getPropertyFile()
    {
        return $this->propertyFile;
    }

    /**
     * @param string $propertyFile
     *
     * @return $this
     * @throws \Exception
     */
    public function setPropertyFile($propertyFile)
    {
        if (!file_exists($this->getDirectory() . DIRECTORY_SEPARATOR . $propertyFile)) {
            throw new \Exception('Specified property file does not exist.');
        }

        $this->propertyFile = $propertyFile;
    }
}
