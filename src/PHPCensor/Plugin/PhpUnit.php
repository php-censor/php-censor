<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin\Option\PhpUnitOptions;
use PHPCensor\Plugin\Util\PhpUnitResultJson;
use PHPCensor\Plugin\Util\PhpUnitResultJunit;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * PHP Unit Plugin - A rewrite of the original PHP Unit plugin
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnit extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var string
     */
    protected $buildDirectory;

    /**
     * @var string
     */
    protected $location;

    /** @var PhpUnitOptions*/
    protected $options;

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

        $this->buildDirectory = $this->build->getProjectId() . '/' . $this->build->getId();
        $this->location       = PUBLIC_DIR . 'artifacts/phpunit/' . $this->buildDirectory . '/coverage';

        $this->options = new PhpUnitOptions($options, $this->location);
    }

    /**
     * Check if the plugin can be executed without any configurations
     *
     * @param string  $stage
     * @param Builder $builder
     * @param Build   $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == Build::STAGE_TEST && !is_null(PhpUnitOptions::findConfigFile($build->getBuildPath()))) {
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

        $cmd      = $this->findBinary('phpunit');
        $lastLine = exec($cmd.' --log-json . --version');
        if (false !== strpos($lastLine, '--log-json')) {
            $logFormat = 'junit'; // --log-json is not supported
        } else {
            $logFormat = 'json';
        }

        $success = [];

        // Run any directories
        if (!empty($directories)) {
            foreach ($directories as $directory) {
                $success[] = $this->runConfig($directory, null, $logFormat);
            }
        } else {
            // Run any config files
            if (!empty($xmlConfigFiles)) {
                foreach ($xmlConfigFiles as $configFile) {
                    $success[] = $this->runConfig($this->options->getTestsPath(), $configFile, $logFormat);
                }
            }
        }

        return !in_array(false, $success);
    }

    /**
     * Run the tests defined in a PHPUnit config file or in a specific directory.
     *
     * @param string      $directory
     * @param string|null $configFile
     * @param string      $logFormat
     *
     * @return bool|mixed
     */
    protected function runConfig($directory, $configFile, $logFormat)
    {
        $options   = clone $this->options;
        $buildPath = $this->build->getBuildPath();

        // Save the results into a log file
        $logFile = @tempnam($buildPath, 'jLog_');
        $options->addArgument('log-' . $logFormat, $logFile);

        // Removes any current configurations files
        $options->removeArgument('configuration');
        if (null !== $configFile) {
            // Only the add the configuration file been passed
            $options->addArgument('configuration', $buildPath . $configFile);
        }

        if (!file_exists($this->location) && $options->getOption('coverage')) {
            mkdir($this->location, (0777 & ~umask()), true);
        }

        $arguments = $this->builder->interpolate($options->buildArgumentString());
        $cmd       = $this->findBinary('phpunit') . ' %s %s';
        $success   = $this->builder->executeCommand($cmd, $arguments, $directory);
        $output    = $this->builder->getLastOutput();

        $this->processResults($logFile, $logFormat);

        $config = $this->builder->getSystemConfig('php-censor');

        if ($options->getOption('coverage')) {
            preg_match(
                '#Classes:[\s]*(.*?)%[^M]*?Methods:[\s]*(.*?)%[^L]*?Lines:[\s]*(.*?)\%#s',
                $output,
                $matches
            );

            $this->build->storeMeta('phpunit-coverage', [
                'classes' => !empty($matches[1]) ? $matches[1] : '0.00',
                'methods' => !empty($matches[2]) ? $matches[2] : '0.00',
                'lines'   => !empty($matches[3]) ? $matches[3] : '0.00',
            ]);

            $this->builder->logSuccess(
                sprintf(
                    "\nPHPUnit successful.\nYou can use coverage report: %s",
                    $config['url'] . '/artifacts/phpunit/' . $this->buildDirectory . '/coverage/index.html'
                )
            );
        }

        return $success;
    }

    /**
     * Saves the test results
     *
     * @param string $logFile
     * @param string $logFormat
     *
     * @throws \Exception If failed to parse the log file
     */
    protected function processResults($logFile, $logFormat)
    {
        if (file_exists($logFile)) {
            if ('json' === $logFormat) {
                $parser = new PhpUnitResultJson($logFile, $this->build->getBuildPath());
            } else {
                $parser = new PhpUnitResultJunit($logFile, $this->build->getBuildPath());
            }

            $this->build->storeMeta('phpunit-data', $parser->parse()->getResults());
            $this->build->storeMeta('phpunit-errors', $parser->getFailures());

            foreach ($parser->getErrors() as $error) {
                $severity = $error['severity'] ==
                    $parser::SEVERITY_ERROR ?
                        BuildError::SEVERITY_CRITICAL :
                        BuildError::SEVERITY_HIGH;
                $this->build->reportError(
                    $this->builder, 'php_unit', $error['message'], $severity, $error['file'], $error['line']
                );
            }
            @unlink($logFile);
        } else {
            throw new \Exception('log output file does not exist: ' . $logFile);
        }
    }
}
