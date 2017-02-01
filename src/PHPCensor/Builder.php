<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor;

use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Helper\Lang;
use PHPCensor\Helper\MailerFactory;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use b8\Config;
use b8\Store\Factory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use PHPCensor\Plugin\Util\Factory as PluginFactory;

/**
 * PHPCI Build Runner
 * @author   Dan Cryer <dan@block8.co.uk>
 */
class Builder implements LoggerAwareInterface
{
    /**
     * @var string
     */
    public $buildPath;

    /**
     * @var string[]
     */
    public $ignore = [];

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var bool
     */
    protected $verbose = true;

    /**
     * @var \PHPCensor\Model\Build
     */
    protected $build;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $lastOutput;

    /**
     * @var BuildInterpolator
     */
    protected $interpolator;

    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $store;

    /**
     * @var bool
     */
    public $quiet = false;

    /**
     * @var \PHPCensor\Plugin\Util\Executor
     */
    protected $pluginExecutor;

    /**
     * @var Helper\CommandExecutorInterface
     */
    protected $commandExecutor;

    /**
     * @var Logging\BuildLogger
     */
    protected $buildLogger;

    /**
     * Set up the builder.
     *
     * @param \PHPCensor\Model\Build $build
     * @param LoggerInterface        $logger
     */
    public function __construct(Build $build, LoggerInterface $logger = null)
    {
        $this->build = $build;
        $this->store = Factory::getStore('Build', 'PHPCensor');

        $this->buildLogger    = new BuildLogger($logger, $build);
        $pluginFactory        = $this->buildPluginFactory($build);
        $this->pluginExecutor = new Plugin\Util\Executor($pluginFactory, $this->buildLogger);

        $executorClass = 'PHPCensor\Helper\UnixCommandExecutor';
        if (IS_WIN) {
            $executorClass = 'PHPCensor\Helper\WindowsCommandExecutor';
        }

        $this->commandExecutor = new $executorClass(
            $this->buildLogger,
            ROOT_DIR,
            $this->quiet,
            $this->verbose
        );

        $this->interpolator = new BuildInterpolator();
    }

    /**
     * Set the config array, as read from .php-censor.yml
     * @param array|null $config
     * @throws \Exception
     */
    public function setConfigArray($config)
    {
        if (is_null($config) || !is_array($config)) {
            throw new \Exception(Lang::get('missing_app_yml'));
        }

        $this->config = $config;
    }

    /**
     * Access a variable from the .php-censor.yml file.
     * @param string
     * @return mixed
     */
    public function getConfig($key)
    {
        $rtn = null;

        if (isset($this->config[$key])) {
            $rtn = $this->config[$key];
        }

        return $rtn;
    }

    /**
     * Access a variable from the config.yml
     * @param $key
     * @return mixed
     */
    public function getSystemConfig($key)
    {
        return Config::getInstance()->get($key);
    }

    /**
     * @return string   The title of the project being built.
     */
    public function getBuildProjectTitle()
    {
        return $this->build->getProject()->getTitle();
    }

    /**
     * Run the active build.
     */
    public function execute()
    {
        // Update the build in the database, ping any external services.
        $this->build->setStatus(Build::STATUS_RUNNING);
        $this->build->setStarted(new \DateTime());
        $this->store->save($this->build);
        $this->build->sendStatusPostback();
        $success = true;

        $previous_build = $this->build->getProject()->getPreviousBuild($this->build->getBranch());

        $previous_state = Build::STATUS_PENDING;

        if ($previous_build) {
            $previous_state = $previous_build->getStatus();
        }

        try {
            // Set up the build:
            $this->setupBuild();

            // Run the core plugin stages:
            foreach (['setup', 'test'] as $stage) {
                $success &= $this->pluginExecutor->executePlugins($this->config, $stage);
            }

            // Set the status so this can be used by complete, success and failure
            // stages.
            if ($success) {
                $this->build->setStatus(Build::STATUS_SUCCESS);
            } else {
                $this->build->setStatus(Build::STATUS_FAILED);
            }


            if ($success) {
                $this->pluginExecutor->executePlugins($this->config, 'success');

                if ($previous_state == Build::STATUS_FAILED) {
                    $this->pluginExecutor->executePlugins($this->config, 'fixed');
                }
            } else {
                $this->pluginExecutor->executePlugins($this->config, 'failure');

                if ($previous_state == Build::STATUS_SUCCESS || $previous_state == Build::STATUS_PENDING) {
                    $this->pluginExecutor->executePlugins($this->config, 'broken');
                }
            }
        } catch (\Exception $ex) {
            $this->build->setStatus(Build::STATUS_FAILED);
            $this->buildLogger->logFailure(Lang::get('exception') . $ex->getMessage(), $ex);
        }

        if (Build::STATUS_FAILED === $this->build->getStatus()) {
            $this->buildLogger->logFailure("\n" . Lang::get('build_failed'));
        } else {
            $this->buildLogger->logSuccess("\n" . Lang::get('build_success'));
        }

        try {
            // Complete stage plugins are always run
            $this->pluginExecutor->executePlugins($this->config, 'complete');
        } catch (\Exception $ex) {
            $this->buildLogger->logFailure(Lang::get('exception') . $ex->getMessage());
        }

        // Update the build in the database, ping any external services, etc.
        $this->build->sendStatusPostback();
        $this->build->setFinished(new \DateTime());

        $removeBuilds = (bool)Config::getInstance()->get('php-censor.build.remove_builds', true);
        if ($removeBuilds) {
            // Clean up:
            $this->buildLogger->log("\n" . Lang::get('removing_build'));
            $this->build->removeBuildDirectory();
        }

        $this->store->save($this->build);
    }

    /**
     * Used by this class, and plugins, to execute shell commands.
     */
    public function executeCommand()
    {
        return $this->commandExecutor->executeCommand(func_get_args());
    }

    /**
     * Returns the output from the last command run.
     */
    public function getLastOutput()
    {
        return $this->commandExecutor->getLastOutput();
    }

    /**
     * Specify whether exec output should be logged.
     * @param bool $enableLog
     */
    public function logExecOutput($enableLog = true)
    {
        $this->commandExecutor->logExecOutput = $enableLog;
    }

    /**
     * Find a binary required by a plugin.
     * @param string $binary
     * @param bool $quiet
     *
     * @return null|string
     */
    public function findBinary($binary, $quiet = false)
    {
        return $this->commandExecutor->findBinary($binary, $quiet);
    }

    /**
     * Replace every occurrence of the interpolation vars in the given string
     * Example: "This is build %PHPCI_BUILD%" => "This is build 182"
     * @param string $input
     * @return string
     */
    public function interpolate($input)
    {
        return $this->interpolator->interpolate($input);
    }

    /**
     * Set up a working copy of the project for building.
     */
    protected function setupBuild()
    {
        $this->buildPath = $this->build->getBuildPath();

        $this->interpolator->setupInterpolationVars(
            $this->build,
            $this->buildPath,
            APP_URL
        );

        $this->commandExecutor->setBuildPath($this->buildPath);

        // Create a working copy of the project:
        if (!$this->build->createWorkingCopy($this, $this->buildPath)) {
            throw new \Exception(Lang::get('could_not_create_working'));
        }

        // Does the project's .php-censor.yml request verbose mode?
        if (!isset($this->config['build_settings']['verbose']) || !$this->config['build_settings']['verbose']) {
            $this->verbose = false;
        }

        // Does the project have any paths it wants plugins to ignore?
        if (isset($this->config['build_settings']['ignore'])) {
            $this->ignore = $this->config['build_settings']['ignore'];
        }

        $this->buildLogger->logSuccess(Lang::get('working_copy_created', $this->buildPath));
        return true;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->buildLogger->setLogger($logger);
    }

    /**
     * Write to the build log.
     * @param $message
     * @param string $level
     * @param array $context
     */
    public function log($message, $level = LogLevel::INFO, $context = [])
    {
        $this->buildLogger->log($message, $level, $context);
    }

    /**
     * Add a success-coloured message to the log.
     *
     * @param string
     */
    public function logSuccess($message)
    {
        $this->buildLogger->logSuccess($message);
    }

    /**
     * Add a failure-coloured message to the log.
     *
     * @param string $message
     * @param \Exception $exception The exception that caused the error.
     */
    public function logFailure($message, \Exception $exception = null)
    {
        $this->buildLogger->logFailure($message, $exception);
    }

    /**
     * Add a debug message to the log.
     *
     * @param string
     */
    public function logDebug($message)
    {
        $this->buildLogger->logDebug($message);
    }

    /**
     * Returns a configured instance of the plugin factory.
     *
     * @param Build $build
     * @return PluginFactory
     */
    private function buildPluginFactory(Build $build)
    {
        $pluginFactory = new PluginFactory();

        $self = $this;
        $pluginFactory->registerResource(
            function () use ($self) {
                return $self;
            },
            null,
            'PHPCensor\Builder'
        );

        $pluginFactory->registerResource(
            function () use ($build) {
                return $build;
            },
            null,
            'PHPCensor\Model\Build'
        );

        $logger = $this->logger;
        $pluginFactory->registerResource(
            function () use ($logger) {
                return $logger;
            },
            null,
            'Psr\Log\LoggerInterface'
        );

        $pluginFactory->registerResource(
            function () use ($self) {
                $factory = new MailerFactory($self->getSystemConfig('php-censor'));
                return $factory->getSwiftMailerFromConfig();
            },
            null,
            'Swift_Mailer'
        );

        return $pluginFactory;
    }
}
