<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin\Option\PhpUnitOptions;
use PHPCensor\Plugin\Util\PhpUnitResult;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Unit Plugin - A rewrite of the original PHP Unit plugin
 *
 * @author       Dan Cryer <dan@block8.co.uk>
 * @author       Pablo Tejada <pablo@ptejada.com>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class PhpUnit extends Plugin implements ZeroConfigPluginInterface
{
    /** @var string[] Raw options from the PHPCI config file */
    protected $options = [];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'php_unit';
    }
    
    /**
     * Standard Constructor
     * $options['config']    Path to a PHPUnit XML configuration file.
     * $options['run_from']  The directory where the phpunit command will run from when using 'config'.
     * $options['coverage']  Value for the --coverage-html command line flag.
     * $options['directory'] Optional directory or list of directories to run PHPUnit on.
     * $options['args']      Command line args (in string format) to pass to PHP Unit
     *
     * @param Builder  $builder
     * @param Build    $build
     * @param string[] $options
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->options = new PhpUnitOptions($options);
    }

    /**
     * Check if the plugin can be executed without any configurations
     *
     * @param         $stage
     * @param Builder $builder
     * @param Build   $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == 'test' && !is_null(PhpUnitOptions::findConfigFile($build->getBuildPath()))) {
            return true;
        }

        return false;
    }

    /**
     * Runs PHP Unit tests in a specified directory, optionally using specified config file(s).
     */
    public function execute()
    {
        $xmlConfigFiles = $this->options->getConfigFiles($this->build->getBuildPath());
        $directories    = $this->options->getDirectories();
        if (empty($xmlConfigFiles) && empty($directories)) {
            $this->builder->logFailure('Neither a configuration file nor a test directory found.');
            return false;
        }

        $success = [];

        // Run any directories
        if (!empty($directories)) {
            foreach ($directories as $directory) {
                $success[] = $this->runDir($directory);
            }
        } else {
            // Run any config files
            if (!empty($xmlConfigFiles)) {
                foreach ($xmlConfigFiles as $configFile) {
                    $success[] = $this->runConfigFile($configFile);
                }
            }
        }

        return !in_array(false, $success);
    }

    /**
     * Run the PHPUnit tests in a specific directory or array of directories.
     *
     * @param $directory
     *
     * @return bool|mixed
     */
    protected function runDir($directory)
    {
        $options = clone $this->options;

        $buildPath = $this->build->getBuildPath() . DIRECTORY_SEPARATOR;

        // Save the results into a json file
        $jsonFile = @tempnam($buildPath, 'jLog_');
        $options->addArgument('log-json', $jsonFile);

        // Removes any current configurations files
        $options->removeArgument('configuration');

        $arguments = $this->builder->interpolate($options->buildArgumentString());
        $cmd       = $this->builder->findBinary('phpunit') . ' %s "%s"';
        $success   = $this->builder->executeCommand($cmd, $arguments, $directory);

        $this->processResults($jsonFile);

        return $success;
    }

    /**
     * Run the tests defined in a PHPUnit config file.
     *
     * @param $configFile
     *
     * @return bool|mixed
     */
    protected function runConfigFile($configFile)
    {
        $options   = clone $this->options;
        $buildPath = $this->build->getBuildPath() . DIRECTORY_SEPARATOR;

        // Save the results into a json file
        $jsonFile = @tempnam($buildPath, 'jLog_');
        $options->addArgument('log-json', $jsonFile);

        // Removes any current configurations files
        $options->removeArgument('configuration');
        // Only the add the configuration file been passed
        $options->addArgument('configuration', $buildPath . $configFile);

        $arguments = $this->builder->interpolate($options->buildArgumentString());
        $cmd       = $this->builder->findBinary('phpunit') . ' %s %s';
        $success   = $this->builder->executeCommand($cmd, $arguments, $options->getTestsPath());

        $this->processResults($jsonFile);

        return $success;
    }

    /**
     * Saves the test results
     *
     * @param string $jsonFile
     *
     * @throws \Exception If the failed to parse the JSON file
     */
    protected function processResults($jsonFile)
    {
        if (file_exists($jsonFile)) {
            $parser = new PhpUnitResult($jsonFile, $this->build->getBuildPath());

            $this->build->storeMeta('phpunit-data', $parser->parse()->getResults());
            $this->build->storeMeta('phpunit-errors', $parser->getFailures());

            foreach ($parser->getErrors() as $error) {
                $severity = $error['severity'] == $parser::SEVERITY_ERROR ? BuildError::SEVERITY_CRITICAL : BuildError::SEVERITY_HIGH;
                $this->build->reportError(
                    $this->builder, 'php_unit', $error['message'], $severity, $error['file'], $error['line']
                );
            }
            @unlink($jsonFile);
        } else {
            throw new \Exception('JSON output file does not exist: ' . $jsonFile);
        }
    }
}
