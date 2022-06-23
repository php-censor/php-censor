<?php

namespace PHPCensor\Plugin;

use Exception;
use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;

/**
 * A pair programming partner for writing better PHP. https://github.com/wata727/pahout
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Pahout extends Plugin
{
    /** @var string */
    public const TAB = "\t";

    /** @var string */
    protected $directory;

    /** @var int */
    protected $allowedWarnings;

    /**
     * @throws Exception
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->executable = $this->findBinary(['pahout', 'pahout.phar']);

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

        if (!$this->build->isDebug()) {
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
                $this->builder->logFailure('HINT: ' . $hint['full_message'] . PHP_EOL);

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    $hint['message'],
                    PHPCensor\Common\Build\BuildErrorInterface::SEVERITY_LOW,
                    $hint['file'],
                    (int)$hint['line_from']
                );
            }
        }

        if ($success) {
            $this->builder->logSuccess('Awesome! There is nothing from me to teach you!');
        }

        $this->builder->logNormal(\sprintf('%d files checked, %d hints detected.', \count($files), \count($hints)));

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
     *
     * @return bool
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        return PHPCensor\Common\Build\BuildInterface::STAGE_TEST === $stage;
    }

    /**
     * @param string $output
     * @return array
     */
    protected function processReport($output)
    {
        $data = \json_decode(\trim($output), true);

        $hints = [];
        $files = [];

        if (!empty($data) && \is_array($data) && isset($data['hints'])) {
            $files = $data['files'];

            foreach ($data['hints'] as $hint) {
                $hints[] = [
                    'full_message' => \vsprintf('%s:%d' . PHP_EOL . self::TAB . '%s: %s [%s]', [
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
