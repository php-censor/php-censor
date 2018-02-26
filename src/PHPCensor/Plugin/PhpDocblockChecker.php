<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Docblock Checker Plugin - Checks your PHP files for appropriate uses of Docblocks
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class PhpDocblockChecker extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var string Based on the assumption the root may not hold the code to be
     * tested, extends the build path.
     */
    protected $path;

    /**
     * @var array - paths to ignore
     */
    protected $ignore;

    protected $skipClasses = false;
    protected $skipMethods = false;

    /**
     * @var integer
     */
    protected $allowedWarnings;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_docblock_checker';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->ignore = $this->builder->ignore;
        $this->path = '';
        $this->allowedWarnings = 0;

        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowedWarnings = -1;
        }

        if (array_key_exists('skip_classes', $options)) {
            $this->skipClasses = true;
        }

        if (array_key_exists('skip_methods', $options)) {
            $this->skipMethods = true;
        }

        if (!empty($options['path'])) {
            $this->path = $options['path'];
        }

        if (array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }
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
     * Runs PHP Mess Detector in a specified directory.
     */
    public function execute()
    {
        // Check that the binary exists:
        $checker = $this->findBinary('phpdoccheck');

        // Build ignore string:
        $ignore = '';
        if (count($this->ignore)) {
            $ignore = ' --exclude="' . implode(',', $this->ignore) . '"';
        }

        // Are we skipping any checks?
        $add = '';
        if ($this->skipClasses) {
            $add .= ' --skip-classes';
        }

        if ($this->skipMethods) {
            $add .= ' --skip-methods';
        }

        // Build command string:
        $path = $this->builder->buildPath . $this->path;
        $cmd = $checker . ' --json --directory="%s"%s%s';

        // Disable exec output logging, as we don't want the XML report in the log:
        $this->builder->logExecOutput(false);

        // Run checker:
        $this->builder->executeCommand(
            $cmd,
            $path,
            $ignore,
            $add
        );

        // Re-enable exec output logging:
        $this->builder->logExecOutput(true);

        $output  = json_decode($this->builder->getLastOutput(), true);
        $errors  = count($output);
        $success = true;

        $this->build->storeMeta('phpdoccheck-warnings', $errors);
        $this->reportErrors($output);

        if ($this->allowedWarnings != -1 && $errors > $this->allowedWarnings) {
            $success = false;
        }

        return $success;
    }

    /**
     * Report all of the errors we've encountered line-by-line.
     * @param array $output
     */
    protected function reportErrors($output)
    {
        foreach ($output as $error) {
            switch ($error['type']) {
                case 'class':
                    $message = 'Class ' . $error['class'] . ' is missing a docblock.';
                    $severity = BuildError::SEVERITY_NORMAL;
                    break;

                case 'method':
                    $message = $error['class'] . '::' . $error['method'] . ' is missing a docblock.';
                    $severity = BuildError::SEVERITY_NORMAL;
                    break;

                case 'param-missing':
                    $message = $error['class'] . '::' . $error['method'] . ' @param ' . $error['param'] . ' missing.';
                    $severity = BuildError::SEVERITY_LOW;
                    break;

                case 'param-mismatch':
                    $message = $error['class'] . '::' . $error['method'] . ' @param ' . $error['param'] .
                        '(' . $error['doc-type'] . ') does not match method signature (' . $error['param-type'] . ')';
                    $severity = BuildError::SEVERITY_LOW;
                    break;

                case 'return-missing':
                    $message = $error['class'] . '::' . $error['method'] . ' @return missing.';
                    $severity = BuildError::SEVERITY_LOW;
                    break;

                case 'return-mismatch':
                    $message = $error['class'] . '::' . $error['method'] . ' @return ' . $error['doc-type'] .
                        ' does not match method signature (' . $error['return-type'] . ')';
                    $severity = BuildError::SEVERITY_LOW;
                    break;

                default:
                    $message = 'Class ' . $error['class'] . ' invalid/missing a docblock.';
                    $severity = BuildError::SEVERITY_LOW;
                    break;
            }

            $this->build->reportError(
                $this->builder,
                'php_docblock_checker',
                $message,
                $severity,
                $error['file'],
                $error['line']
            );
        }
    }
}
