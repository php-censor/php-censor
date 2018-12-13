<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Mess Detector Plugin - Allows PHP Mess Detector testing.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class PhpMessDetector extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var array
     */
    protected $suffixes;

    /**
     * @var string, based on the assumption the root may not hold the code to be
     * tested, extends the base path only if the provided path is relative. Absolute
     * paths are used verbatim
     */
    protected $directory;

    /**
     * @var array - paths to ignore
     */
    protected $ignore;

    /**
     * Array of PHPMD rules. Can be one of the builtins (codesize, unusedcode, naming, design, controversial)
     * or a filename (detected by checking for a / in it), either absolute or relative to the project root.
     * @var array
     */
    protected $rules;
    protected $allowedWarnings;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_mess_detector';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->suffixes         = ['php'];
        $this->ignore           = $this->builder->ignore;
        $this->rules            = ['codesize', 'unusedcode', 'naming'];
        $this->allowedWarnings = 0;
        $this->directory = $this->builder->directory;


        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowedWarnings = -1;
        }

        if (isset($options['directory']) && !empty($options['directory'])) {
            $this->directory = $this->getWorkingDirectory($options);
        }

        if (array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }

        if (isset($options['executable'])) {
            $this->executable = $options['executable'];
        } else {
            $this->executable = $this->findBinary('phpmd');
        }

        if (array_key_exists('ignore', $options)) {
            $this->ignore = array_unshift($this->ignore, $options['ignore']);
        }

        foreach (['rules', 'suffixes'] as $key) {
            $this->overrideSetting($options, $key);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        if ($stage == Build::STAGE_TEST) {
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

        $errorCount = $this->processReport(trim($this->builder->getLastOutput()));
        $this->build->storeMeta((self::pluginName() . '-warnings'), $errorCount);

        return $this->wasLastExecSuccessful($errorCount);
    }

    /**
     * Override a default setting.
     * @param $options
     * @param $key
     */
    protected function overrideSetting($options, $key)
    {
        if (isset($options[$key]) && is_array($options[$key])) {
            $this->{$key} = $options[$key];
        }
    }

    /**
     * Process PHPMD's XML output report.
     *
     * @param $xmlString
     *
     * @return integer
     *
     * @throws \Exception
     */
    protected function processReport($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            $this->builder->log($xmlString);
            throw new \Exception('Could not process PHPMD report XML.');
        }

        $warnings = 0;

        foreach ($xml->file as $file) {
            $fileName = (string)$file['name'];
            $fileName = str_replace($this->builder->buildPath, '', $fileName);

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
        if (!empty($this->rules) && !is_array($this->rules)) {
            $this->builder->logFailure('The "rules" option must be an array.');
            return false;
        }

        foreach ($this->rules as &$rule) {
            if (strpos($rule, '/') !== false) {
                $rule = $this->builder->buildPath . $rule;
            }
        }

        return true;
    }

    /**
     * Execute PHP Mess Detector.
     * @param $binaryPath
     */
    protected function executePhpMd($binaryPath)
    {
        $cmd = $binaryPath . ' "%s" xml %s %s %s';

        $ignore = '';
        if (count($this->ignore)) {
            $ignore = ' --exclude ' . implode(',', $this->ignore);
        }

        $suffixes = '';
        if (count($this->suffixes)) {
            $suffixes = ' --suffixes ' . implode(',', $this->suffixes);
        }

        // Disable exec output logging, as we don't want the XML report in the log:
        $this->builder->logExecOutput(false);

        // Run PHPMD:
        $this->builder->executeCommand(
            $cmd,
            $this->directory,
            implode(',', $this->rules),
            $ignore,
            $suffixes
        );

        // Re-enable exec output logging:
        $this->builder->logExecOutput(true);
    }



    /**
     * Returns a boolean indicating if the error count can be considered a success.
     *
     * @param int $errorCount
     * @return bool
     */
    protected function wasLastExecSuccessful($errorCount)
    {
        $success = true;

        if ($this->allowedWarnings != -1 && $errorCount > $this->allowedWarnings) {
            $success = false;
            return $success;
        }
        return $success;
    }
}
