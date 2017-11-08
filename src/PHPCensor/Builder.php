<?php

namespace PHPCensor;

use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Helper\MailerFactory;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use b8\Config;
use b8\Store\Factory;
use PHPCensor\Store\BuildErrorWriter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use PHPCensor\Plugin\Util\Factory as PluginFactory;

/**
 * @author Dan Cryer <dan@block8.co.uk>
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
    protected $config = [];

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
     * @var BuildErrorWriter
     */
    private $buildErrorWriter;

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

        $executorClass         = 'PHPCensor\Helper\CommandExecutor';
        $this->commandExecutor = new $executorClass(
            $this->buildLogger,
            ROOT_DIR,
            $this->quiet,
            $this->verbose
        );

        $this->interpolator     = new BuildInterpolator();
        $this->buildErrorWriter = new BuildErrorWriter($this->build->getId());
    }

    /**
     * Set the config array, as read from .php-censor.yml
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfigArray($config)
    {
        if (is_null($config) || !is_array($config)) {
            throw new \Exception('This project does not contain a .php-censor.yml (.phpci.yml|phpci.yml) file, or it is empty.');
        }

        $this->logDebug('Config: ' . json_encode($config));
        $this->config = $config;
    }

    /**
     * Access a variable from the .php-censor.yml file.
     *
     * @param string $key
     *
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
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getSystemConfig($key)
    {
        return Config::getInstance()->get($key);
    }

    /**
     * @return string The title of the project being built.
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
        // check current status
        if ($this->build->getStatus() != Build::STATUS_PENDING) {
            throw new BuilderException('Can`t build - status is not pending', BuilderException::FAIL_START);
        }
        // set status only if current status pending
        if (!$this->build->setStatusSync(Build::STATUS_RUNNING)) {
            throw new BuilderException('Can`t build - unable change status to running', BuilderException::FAIL_START);
        }

        // Update the build in the database, ping any external services.
        $this->build->setStartDate(new \DateTime());
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
            foreach ([Build::STAGE_SETUP, Build::STAGE_TEST, Build::STAGE_DEPLOY] as $stage) {
                $success &= $this->pluginExecutor->executePlugins($this->config, $stage);
                if (!$success) {
                    break;
                }
            }

            // Set the status so this can be used by complete, success and failure
            // stages.
            if ($success) {
                $this->build->setStatus(Build::STATUS_SUCCESS);
            } else {
                $this->build->setStatus(Build::STATUS_FAILED);
            }
        } catch (\Exception $ex) {
            $success = false;
            $this->build->setStatus(Build::STATUS_FAILED);
            $this->buildLogger->logFailure('Exception: ' . $ex->getMessage(), $ex);
        }

        try {
            if ($success) {
                $this->pluginExecutor->executePlugins($this->config, Build::STAGE_SUCCESS);

                if ($previous_state == Build::STATUS_FAILED) {
                    $this->pluginExecutor->executePlugins($this->config, Build::STAGE_FIXED);
                }
            } else {
                $this->pluginExecutor->executePlugins($this->config, Build::STAGE_FAILURE);

                if ($previous_state == Build::STATUS_SUCCESS || $previous_state == Build::STATUS_PENDING) {
                    $this->pluginExecutor->executePlugins($this->config, Build::STAGE_BROKEN);
                }
            }
        } catch (\Exception $ex) {
            $this->buildLogger->logFailure('Exception: ' . $ex->getMessage(), $ex);
        }

        if (Build::STATUS_FAILED === $this->build->getStatus()) {
            $this->buildLogger->logFailure("\nBUILD FAILED");
        } else {
            $this->buildLogger->logSuccess("\nBUILD SUCCESS");
        }

        try {
            // Complete stage plugins are always run
            $this->pluginExecutor->executePlugins($this->config, Build::STAGE_COMPLETE);
        } catch (\Exception $ex) {
            $this->buildLogger->logFailure('Exception: ' . $ex->getMessage());
        }

        // Update the build in the database, ping any external services, etc.
        $this->build->sendStatusPostback();
        $this->build->setFinishDate(new \DateTime());

        $removeBuilds = (bool)Config::getInstance()->get('php-censor.build.remove_builds', true);
        if ($removeBuilds) {
            // Clean up:
            $this->buildLogger->log("\nRemoving Build.");
            $this->build->removeBuildDirectory();
        }

        $this->buildErrorWriter->flush();
        $this->store->save($this->build);
    }

    /**
     * Used by this class, and plugins, to execute shell commands.
     *
     * @return boolean
     */
    public function executeCommand()
    {
        return $this->commandExecutor->executeCommand(func_get_args());
    }

    /**
     * Returns the output from the last command run.
     *
     * @return string
     */
    public function getLastOutput()
    {
        return $this->commandExecutor->getLastOutput();
    }

    /**
     * Specify whether exec output should be logged.
     *
     * @param boolean $enableLog
     */
    public function logExecOutput($enableLog = true)
    {
        $this->commandExecutor->logExecOutput = $enableLog;
    }

    /**
     * Find a binary required by a plugin.
     *
     * @param string $binary
     * @param bool   $quiet Returns null instead of throwing an exception.
     * @param string $priorityPath
     *
     * @return null|string
     *
     * @throws \Exception when no binary has been found and $quiet is false.
     */
    public function findBinary($binary, $quiet = false, $priorityPath = 'local')
    {
        return $this->commandExecutor->findBinary($binary, $quiet, $priorityPath);
    }

    /**
     * Replace every occurrence of the interpolation vars in the given string
     * Example: "This is build %PHPCI_BUILD%" => "This is build 182"
     *
     * @param string $input
     *
     * @return string
     */
    public function interpolate($input)
    {
        return $this->interpolator->interpolate($input);
    }

    /**
     * Set up a working copy of the project for building.
     *
     * @throws \Exception
     *
     * @return boolean
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
            throw new \Exception('Could not create a working copy.');
        }

        // Does the project's .php-censor.yml request verbose mode?
        if (!isset($this->config['build_settings']['verbose']) || !$this->config['build_settings']['verbose']) {
            $this->verbose = false;
        }

        // Does the project have any paths it wants plugins to ignore?
        if (isset($this->config['build_settings']['ignore'])) {
            $this->ignore = $this->config['build_settings']['ignore'];
        }

        $this->buildLogger->logSuccess(sprintf('Working copy created: %s', $this->buildPath));

        return true;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->buildLogger->setLogger($logger);
    }

    /**
     * Write to the build log.
     *
     * @param string $message
     * @param string $level
     * @param array  $context
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
     * @param string     $message
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
     *
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

    /**
     * @return BuildErrorWriter
     */
    public function getBuildErrorWriter()
    {
        return $this->buildErrorWriter;
    }
}
