<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;

/**
 * A pair programming partner for writing better PHP.
 * https://github.com/wata727/pahout
 */
class Pahout extends Plugin
{
    /** @var string */
    const TAB = "\t";

    /** @var string */
    protected $directory;

    /** @var int */
    protected $allowedWarnings;

    /**
     * @param Builder $builder
     * @param Build $build
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->findBinary('pahout');

        if (!empty($options['directory']) && \is_string($options['directory'])) {
            $this->directory = $options['directory'];
        } else {
            $this->directory = './src';
        }

        if (isset($options['allowed_warnings']) && \is_int($options['allowed_warnings'])) {
            $this->allowedWarnings = $options['allowed_warnings'];
        } else {
            $this->allowedWarnings = -1;
        }
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $pahout = $this->executable;

        if ((!\defined('DEBUG_MODE') || !DEBUG_MODE) && !(bool)$this->build->getExtra('debug')) {
            $this->builder->logExecOutput(false);
        }

        $this->builder->executeCommand(
            'cd "%s" && ' . $pahout . ' %s --format=json',
            $this->builder->buildPath,
            $this->directory
        );
        $this->builder->logExecOutput(true);

        // Define that the plugin succeed
        $success = true;

        list($files, $hints) = $this->processReport($this->builder->getLastOutput());

        if (0 < \count($hints)) {
            if (-1 !== $this->allowedWarnings && \count($hints) > $this->allowedWarnings) {
                $success = false;
            }

            foreach ($hints as $hint) {
                $this->builder->logFailure('HINT: ' . $hint['full_message'] . \PHP_EOL);

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    $hint['message'],
                    BuildError::SEVERITY_LOW,
                    $hint['file'],
                    $hint['line_from']
                );
            }
        }

        if ($success) {
            $this->builder->logSuccess('Awesome! There is nothing from me to teach you!');
        }

        $this->builder->log(\sprintf('%d files checked, %d hints detected.', \count($files), \count($hints)));

        return $success;
    }

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'pahout';
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
    protected function processReport($output)
    {
        $data = \json_decode(trim($output), true);

        $hints = [];
        $files = [];

        if (!empty($data) && \is_array($data) && isset($data['hints'])) {
            $files = $data['files'];

            foreach ($data['hints'] as $hint) {
                $hints[] = [
                    'full_message' => \vsprintf('%s:%d' . \PHP_EOL . self::TAB . '%s: %s [%s]', [
                        $hint['filename'],
                        $hint['lineno'],
                        $hint['type'],
                        $hint['message'],
                        $hint['link']
                    ]),
                    'message'   => $hint['message'],
                    'file'      => $hint['filename'],
                    'line_from' => $hint['lineno']
                ];
            }
        }

        return [$files, $hints];
    }
}
