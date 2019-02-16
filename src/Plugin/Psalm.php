<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;

/**
 * A static analysis tool for finding errors in PHP applications https://getpsalm.org
 *
 * Class Psalm
 * @package PHPCensor\Plugin
 */
class Psalm extends Plugin
{
    /**
     * @var int
     */
    protected $allowedErrors;

    /**
     * @var int
     */
    protected $allowedWarnings;

    /**
     * Psalm constructor.
     * @param Builder $builder
     * @param Build $build
     * @param array $options
     * @throws \Exception
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->findBinary('psalm');

        if (!empty($options['allowed_errors']) && \is_int($options['allowed_errors'])) {
            $this->allowedErrors = $options['allowed_errors'];
        } else {
            $this->allowedErrors   = 0;
        }

        if (!empty($options['allowed_warnings']) && \is_int($options['allowed_warnings'])) {
            $this->allowedWarnings = $options['allowed_warnings'];
        } else {
            $this->allowedWarnings = 0;
        }
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $psalm = $this->executable;

        if ((!\defined('DEBUG_MODE') || !DEBUG_MODE) && !(boolean)$this->build->getExtra('debug')) {
            $this->builder->logExecOutput(false);
        }

        $this->builder->executeCommand('cd "%s" && ' . $psalm . ' --output-format=json', $this->builder->buildPath);
        $this->builder->logExecOutput(true);

        // Define that the plugin succeed
        $success = true;

        list($errors, $infos) = $this->processReport($this->builder->getLastOutput());

        if (0 < \count($errors)) {
            if (-1 !== $this->allowedErrors && \count($errors) > $this->allowedErrors) {
                $success = false;
            }

            foreach ($errors as $error) {
                $this->builder->logFailure('ERROR: ' . $error['full_message'] . \PHP_EOL);

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    $error['message'],
                    BuildError::SEVERITY_HIGH,
                    $error['file'],
                    $error['line_from'],
                    $error['line_to']
                );
            }
        }

        if (0 < \count($infos)) {
            if (-1 !== $this->allowedWarnings && \count($infos) > $this->allowedWarnings) {
                $success = false;
            }

            foreach ($infos as $info) {
                $this->builder->logFailure('INFO: ' . $info['full_message'] . \PHP_EOL);

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    $info['message'],
                    BuildError::SEVERITY_LOW,
                    $info['file'],
                    $info['line_from'],
                    $info['line_to']
                );
            }
        }

        if ($success) {
            $this->builder->logSuccess('No errors found!');
        }

        return $success;
    }

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'psalm';
    }

    /**
     * @param string $stage
     * @param Build $build
     * @return bool
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        return Build::STAGE_TEST === $stage;
    }

    /**
     * @param string $output
     * @return array
     */
    protected function processReport(string $output)
    {
        $data = \json_decode(trim($output), true);

        $errors = [];
        $infos = [];

        if (!empty($data) && \is_array($data)) {
            foreach ($data as $value) {
                if (!\in_array($value['severity'], ['error','info'])) {
                    continue;
                }

                ${$value['severity'].'s'}[] = [
                    'full_message' => \vsprintf('%s - %s:%d:%d - %s' . \PHP_EOL . '%s', [
                        $value['type'],
                        $value['file_name'],
                        $value['line_from'],
                        $value['column_from'],
                        $value['message'],
                        $value['snippet']
                    ]),
                    'message' => $value['message'],
                    'file' => $value['file_name'],
                    'line_from' => $value['line_from'],
                    'line_to' => $value['line_to']
                ];
            }
        }

        return [$errors, $infos];
    }
}
