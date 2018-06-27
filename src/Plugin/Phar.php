<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use Phar as PHPPhar;
use PHPCensor\Plugin;

/**
 * Phar Plugin
 */
class Phar extends Plugin
{
    /**
     * Output Directory
     * @var string
     */
    protected $directory;

    /**
     * Phar Filename
     * @var string
     */
    protected $filename;

    /**
     * Regular Expression Filename Capture
     * @var string
     */
    protected $regexp;

    /**
     * Stub Filename
     * @var string
     */
    protected $stub;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'phar';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->directory = $this->getWorkingDirectory($options);

        // Filename?
        if (isset($options['filename'])) {
            $this->setFilename($options['filename']);
        }

        // RegExp?
        if (isset($options['regexp'])) {
            $this->setRegExp($options['regexp']);
        }

        // Stub?
        if (isset($options['stub'])) {
            $this->setStub($options['stub']);
        }
    }

    /**
     * Filename Setter
     *
     * @param  string $filename Configuration Value
     * @return Phar   Fluent Interface
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Filename Getter
     *
     * @return string Configurated or Default Value
     */
    public function getFilename()
    {
        if (!isset($this->filename)) {
            $this->setFilename('build.phar');
        }
        return $this->filename;
    }

    /**
     * Regular Expression Setter
     *
     * @param  string $regexp Configuration Value
     * @return Phar   Fluent Interface
     */
    public function setRegExp($regexp)
    {
        $this->regexp = $regexp;
        return $this;
    }

    /**
     * Regular Expression Getter
     *
     * @return string Configurated or Default Value
     */
    public function getRegExp()
    {
        if (!isset($this->regexp)) {
            $this->setRegExp('/\.php$/');
        }
        return $this->regexp;
    }

    /**
     * Stub Filename Setter
     *
     * @param  string $stub Configuration Value
     * @return Phar   Fluent Interface
     */
    public function setStub($stub)
    {
        $this->stub = $stub;
        return $this;
    }

    /**
     * Stub Filename Getter
     *
     * @return string Configurated Value
     */
    public function getStub()
    {
        return $this->stub;
    }

    /**
     * Get stub content for the Phar file.
     * @return string
     */
    public function getStubContent()
    {
        $content  = '';
        $filename = $this->getStub();
        if ($filename) {
            $content = file_get_contents($this->builder->buildPath . $this->getStub());
        }
        return $content;
    }

    /**
     * Run the phar plugin.
     * @return bool
     */
    public function execute()
    {
        $success = false;

        try {
            $phar = new PHPPhar($this->directory . $this->getFilename(), 0, $this->getFilename());
            $phar->buildFromDirectory($this->builder->buildPath, $this->getRegExp());

            $stub = $this->getStubContent();
            if ($stub) {
                $phar->setStub($stub);
            }

            $success = true;
        } catch (\Exception $e) {
            $this->builder->logFailure('Phar Plugin Internal Error. Exception: ' . $e->getMessage());
        }

        return $success;
    }
}
