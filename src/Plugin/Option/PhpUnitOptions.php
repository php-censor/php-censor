<?php

namespace PHPCensor\Plugin\Option;

use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * Class PhpUnitOptions validates and parse the option for the PhpUnitV2 plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class PhpUnitOptions
{
    protected array $options;

    protected string $location;

    protected array $arguments = [];

    protected ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration, array $options, string $location)
    {
        $this->configuration = $configuration;
        $this->options       = $options;
        $this->location      = $location;
    }

    /**
     * Remove a command argument
     *
     *
     * @return $this
     */
    public function removeArgument($argumentName)
    {
        unset($this->arguments[$argumentName]);

        return $this;
    }

    /**
     * Combine all the argument into a string for the phpunit command
     *
     * @return string
     */
    public function buildArgumentString()
    {
        $argumentString = '';
        foreach ($this->getCommandArguments() as $argumentName => $argumentValues) {
            $prefix = $argumentName[0] === '-' ? '' : '--';

            if (!\is_array($argumentValues)) {
                $argumentValues = [$argumentValues];
            }

            foreach ($argumentValues as $argValue) {
                $postfix = ' ';
                if (!empty($argValue)) {
                    $postfix = ' "' . $argValue . '" ';
                }
                $argumentString .= $prefix . $argumentName . $postfix;
            }
        }

        return $argumentString;
    }

    /**
     * Get all the command arguments
     *
     * @return string[]
     */
    public function getCommandArguments()
    {
        /*
         * Return the full list of arguments
         */
        return $this->parseArguments()->arguments;
    }

    /**
     * Parse the arguments from the config options
     *
     * @return $this
     */
    private function parseArguments()
    {
        if (empty($this->arguments)) {
            /*
             * Parse the arguments from the YML options file
             */
            if (isset($this->options['args'])) {
                $rawArgs = $this->options['args'];
                if (\is_array($rawArgs)) {
                    $this->arguments = $rawArgs;
                } else {
                    /*
                     * Try to parse old arguments in a single string
                     */
                    \preg_match_all('@--([a-z\-]+)([\s=]+)?[\'"]?((?!--)[-\w/.,\\\]+)?[\'"]?@', (string)$rawArgs, $argsMatch);

                    if (!empty($argsMatch) && \sizeof($argsMatch) > 2) {
                        foreach ($argsMatch[1] as $index => $argName) {
                            $this->addArgument($argName, $argsMatch[3][$index]);
                        }
                    }
                }
            }

            /*
             * Handles command aliases outside of the args option
             */
            if (isset($this->options['coverage']) && $this->options['coverage']) {
                $allowPublicArtifacts = (bool)$this->configuration->get(
                    'php-censor.build.allow_public_artifacts',
                    false
                );

                if ($allowPublicArtifacts) {
                    $this->addArgument('coverage-html', $this->location);
                }
                $this->addArgument('coverage-text');
            }

            /*
             * Handles command aliases outside of the args option
             */
            if (isset($this->options['config'])) {
                $this->addArgument('configuration', $this->options['config']);
            }
        }

        return $this;
    }

    /**
     * Add an argument to the collection
     * Note: adding argument before parsing the options will prevent the other options from been parsed.
     *
     * @param string $argumentName
     * @param string $argumentValue
     */
    public function addArgument($argumentName, $argumentValue = null)
    {
        if (isset($this->arguments[$argumentName])) {
            if (!\is_array($this->arguments[$argumentName])) {
                // Convert existing argument values into an array
                $this->arguments[$argumentName] = [$this->arguments[$argumentName]];
            }

            // Appends the new argument to the list
            $this->arguments[$argumentName][] = $argumentValue;
        } else {
            // Adds new argument
            $this->arguments[$argumentName] = $argumentValue;
        }
    }

    /**
     * Get the list of directory to run phpunit in
     *
     * @return string[] List of directories
     */
    public function getDirectories()
    {
        $directories = $this->getOption('directories');

        if (\is_string($directories)) {
            $directories = [$directories];
        } else {
            if (\is_null($directories)) {
                $directories = [];
            }
        }

        return \is_array($directories) ? $directories : [$directories];
    }

    /**
     * Get an option if defined
     *
     *
     * @return string|string[]|null
     */
    public function getOption($optionName)
    {
        if (isset($this->options[$optionName])) {
            return $this->options[$optionName];
        }

        return null;
    }

    /**
     * Get the directory to execute the command from
     *
     * @return mixed|null
     */
    public function getRunFrom()
    {
        return $this->getOption('run_from');
    }

    /**
     * Ge the directory name where tests file reside
     *
     * @return string|null
     */
    public function getTestsPath()
    {
        return $this->getOption('path');
    }

    /**
     * Get the PHPUnit configuration from the options, or the optional path
     *
     * @param string $altPath
     *
     * @return string[] path of files
     */
    public function getConfigFiles($altPath = null)
    {
        $configFiles = $this->getArgument('configuration');
        if (empty($configFiles) && $altPath) {
            $configFile = self::findConfigFile($altPath);
            if ($configFile) {
                $configFiles[] = $configFile;
            }
        }

        return $configFiles;
    }

    /**
     * Get options for a given argument
     *
     *
     * @return string[] All the options for given argument
     */
    public function getArgument($argumentName)
    {
        $this->parseArguments();

        if (isset($this->arguments[$argumentName])) {
            return \is_array(
                $this->arguments[$argumentName]
            ) ? $this->arguments[$argumentName] : [$this->arguments[$argumentName]];
        }

        return [];
    }

    /**
     * Find a PHPUnit configuration file in a directory
     *
     * @param string $buildPath The path to configuration file
     *
     * @return string|null
     */
    public static function findConfigFile($buildPath)
    {
        $files = [
            'phpunit.xml',
            'phpunit.mysql.xml',
            'phpunit.pgsql.xml',
            'phpunit.xml.dist',
            'tests/phpunit.xml',
            'tests/phpunit.xml.dist',
        ];

        foreach ($files as $file) {
            if (\file_exists($buildPath . $file)) {
                return $file;
            }
        }

        return null;
    }
}
