<?php

namespace PHPCensor\Plugin;

use Exception;
use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * PHP Mess Detector Plugin - Allows PHP Mess Detector testing.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class PhpMessDetector extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var array
     */
    protected $suffixes = ['php'];

    /**
     * Array of PHPMD rules. Can be one of the builtins (codesize, unusedcode, naming, design, controversial)
     * or a filename (detected by checking for a / in it), either absolute or relative to the project root.
     * @var array
     */
    protected $rules = ['codesize', 'unusedcode', 'naming'];
    protected $allowedWarnings = 0;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_mess_detector';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowedWarnings = -1;
        }

        if (\array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }

        $this->executable = $this->findBinary(['phpmd', 'phpmd.phar']);

        foreach (['rules', 'suffixes'] as $key) {
            $this->overrideSetting($options, $key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        if (Build::STAGE_TEST === $stage) {
            return true;
        }

        return false;
    }

    /**
     * Runs PHP Mess Detector in a specified directory.
     */
    public function execute()
    {
        if (!$this->tryAndProcessRules()) {
            return false;
        }

        $phpmdBinaryPath = $this->executable;

        $this->executePhpMd($phpmdBinaryPath);

        $errorCount = $this->processReport(\trim($this->builder->getLastOutput()));
        $this->build->storeMeta((self::pluginName() . '-warnings'), $errorCount);

        return $this->wasLastExecSuccessful($errorCount);
    }

    /**
     * Override a default setting.
     */
    protected function overrideSetting($options, $key)
    {
        if (isset($options[$key]) && \is_array($options[$key])) {
            $this->{$key} = $options[$key];
        }
    }

    /**
     * Process PHPMD's XML output report.
     *
     * @return int
     *
     * @throws Exception
     */
    protected function processReport($xmlString)
    {
        $xml = \simplexml_load_string($xmlString);

        if (false === $xml) {
            $this->builder->log($xmlString);

            throw new RuntimeException('Could not process PHPMD report XML.');
        }

        $warnings = 0;

        foreach ($xml->file as $file) {
            $fileName = (string)$file['name'];
            $fileName = \str_replace($this->builder->buildPath, '', $fileName);

            foreach ($file->violation as $violation) {
                $warnings++;

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    (string)$violation,
                    PHPCensor\Model\BuildError::SEVERITY_HIGH,
                    $fileName,
                    (int)$violation['beginline'],
                    (int)$violation['endline']
                );
            }
        }

        return $warnings;
    }

    /**
     * Try and process the rules parameter from .php-censor.yml.
     * @return bool
     */
    protected function tryAndProcessRules()
    {
        if (!empty($this->rules) && !\is_array($this->rules)) {
            $this->builder->logFailure('The "rules" option must be an array.');

            return false;
        }

        foreach ($this->rules as &$rule) {
            if (\strpos($rule, '/') !== false) {
                $rule = $this->builder->buildPath . $rule;
            }
        }

        return true;
    }

    /**
     * Execute PHP Mess Detector.
     */
    protected function executePhpMd($binaryPath)
    {
        $cmd = 'cd "%s" && ' . $binaryPath . ' "%s" xml %s %s %s';

        $ignore = '';
        if (\is_array($this->ignore) && \count($this->ignore) > 0) {
            $ignoreArray = [];
            foreach ($this->ignore as $ignoreItem) {
                $ignoreArray[] = /*$this->builder->buildPath .*/ $ignoreItem;
            }

            $ignore = \sprintf(' --exclude "%s"', \implode(',', $ignoreArray));
        }

        $suffixes = '';
        if (\is_array($this->suffixes) && \count($this->suffixes) > 0) {
            $suffixes = ' --suffixes ' . \implode(',', $this->suffixes);
        }

        if (!$this->build->isDebug()) {
            $this->builder->logExecOutput(false);
        }

        // Run PHPMD:
        $this->builder->executeCommand(
            $cmd,
            $this->builder->buildPath,
            $this->directory,
            \implode(',', $this->rules),
            $ignore,
            $suffixes
        );

        $this->builder->logExecOutput(true);
    }

    /**
     * Returns a bool indicating if the error count can be considered a success.
     *
     * @param int $errorCount
     *
     * @return bool
     */
    protected function wasLastExecSuccessful($errorCount)
    {
        if (-1 !== $this->allowedWarnings && $errorCount > $this->allowedWarnings) {
            return false;
        }

        return true;
    }
}
