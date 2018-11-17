<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Code Sniffer Plugin - Allows PHP Code Sniffer testing.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class PhpCodeSniffer extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var array
     */
    protected $suffixes;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $standard;

    /**
     * @var string
     */
    protected $tabWidth;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var int
     */
    protected $allowedErrors;

    /**
     * @var int
     */
    protected $allowedWarnings;

    /**
     * @var string, based on the assumption the root may not hold the code to be tested, extends the base path
     */
    protected $path;

    /**
     * @var array - paths to ignore
     */
    protected $ignore;

    /**
     * @var int
     */
    protected $severity = null;
    /**
     * @var null|int
     */
    protected $errorSeverity = null;

    /**
     * @var null|int
     */
    protected $warningSeverity = null;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_code_sniffer';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->suffixes         = ['php'];
        $this->directory        = $this->builder->buildPath;
        $this->standard         = 'PSR2';
        $this->tabWidth         = '';
        $this->encoding         = '';
        $this->path             = '';
        $this->ignore           = $this->builder->ignore;
        $this->allowedWarnings  = 0;
        $this->allowedErrors    = 0;

        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowedWarnings = -1;
            $this->allowedErrors   = -1;
        }

        if (!empty($options['allowed_errors']) && is_int($options['allowed_errors'])) {
            $this->allowedErrors = $options['allowed_errors'];
        }

        if (!empty($options['allowed_warnings']) && is_int($options['allowed_warnings'])) {
            $this->allowedWarnings = $options['allowed_warnings'];
        }

        if (isset($options['suffixes'])) {
            $this->suffixes = (array)$options['suffixes'];
        }

        if (!empty($options['tab_width'])) {
            $this->tabWidth = ' --tab-width='.$options['tab_width'];
        }

        if (!empty($options['encoding'])) {
            $this->encoding = ' --encoding=' . $options['encoding'];
        }

        if (!empty($options['ignore'])) {
            $this->ignore = (array)$options['ignore'];
        }

        if (!empty($options['standard'])) {
            $this->standard = $options['standard'];
        }

        if (!empty($options['path'])) {
            $this->path = $options['path'];
        }

        if (isset($options['severity']) && is_int($options['severity'])) {
            $this->severity = $options['severity'];
        }

        if (isset($options['error_severity']) && is_int($options['error_severity'])) {
            $this->errorSeverity = $options['error_severity'];
        }

        if (isset($options['warning_severity']) && is_int($options['warning_severity'])) {
            $this->warningSeverity = $options['warning_severity'];
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
    * Runs PHP Code Sniffer in a specified directory, to a specified standard.
    */
    public function execute()
    {
        list($ignore, $standard, $suffixes, $severity, $errorSeverity, $warningSeverity) = $this->getFlags();

        $phpcs = $this->findBinary('phpcs');

        $this->builder->logExecOutput(false);

        $cmd = $phpcs . ' --report=json %s %s %s %s %s "%s" %s %s %s';
        $this->builder->executeCommand(
            $cmd,
            $standard,
            $suffixes,
            $ignore,
            $this->tabWidth,
            $this->encoding,
            $this->builder->buildPath . $this->path,
            $severity,
            $errorSeverity,
            $warningSeverity
        );

        $output = $this->builder->getLastOutput();
        list($errors, $warnings) = $this->processReport($output);

        $this->builder->logExecOutput(true);

        $success = true;
        $this->build->storeMeta((self::pluginName() . '-warnings'), $warnings);
        $this->build->storeMeta((self::pluginName() . '-errors'), $errors);

        if ($this->allowedWarnings != -1 && $warnings > $this->allowedWarnings) {
            $success = false;
        }

        if ($this->allowedErrors != -1 && $errors > $this->allowedErrors) {
            $success = false;
        }

        return $success;
    }

    /**
     * Process options and produce an arguments string for PHPCS.
     * @return array
     */
    protected function getFlags()
    {
        $ignore = '';
        if (count($this->ignore)) {
            $ignore = ' --ignore=' . implode(',', $this->ignore);
        }

        if (strpos($this->standard, '/') !== false) {
            $standard = ' --standard=' . $this->directory.$this->standard;
        } else {
            $standard = ' --standard=' . $this->standard;
        }

        $suffixes = '';
        if (count($this->suffixes)) {
            $suffixes = ' --extensions=' . implode(',', $this->suffixes);
        }

        $severity = '';
        if ($this->severity !== null) {
            $severity = ' --severity=' . $this->severity;
        }

        $errorSeverity = '';
        if ($this->errorSeverity !== null) {
            $errorSeverity = ' --error-severity=' . $this->errorSeverity;
        }

        $warningSeverity = '';
        if ($this->warningSeverity !== null) {
            $warningSeverity = ' --warning-severity=' . $this->warningSeverity;
        }

        return [$ignore, $standard, $suffixes, $severity, $errorSeverity, $warningSeverity];
    }

    /**
     * Process the PHPCS output report.
     * @param $output
     * @return array
     * @throws \Exception
     */
    protected function processReport($output)
    {
        $data = json_decode(trim($output), true);

        if (!is_array($data)) {
            $this->builder->log($output);
            throw new \Exception('Could not process the report generated by PHP Code Sniffer.');
        }

        $errors   = $data['totals']['errors'];
        $warnings = $data['totals']['warnings'];

        foreach ($data['files'] as $fileName => $file) {
            $fileName = str_replace($this->builder->buildPath, '', $fileName);

            foreach ($file['messages'] as $message) {
                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    'PHPCS: ' . $message['message'],
                    $message['type'] == 'ERROR' ? BuildError::SEVERITY_HIGH : BuildError::SEVERITY_LOW,
                    $fileName,
                    $message['line']
                );
            }
        }

        return [$errors, $warnings];
    }
}
