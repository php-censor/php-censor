<?php

namespace PHPCensor\Plugin;

use Exception;
use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * Sensiolabs Insight Plugin - Allows Sensiolabs Insight testing.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Eugen Ganshorn <eugen@ganshorn.eu>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SensiolabsInsight extends Plugin
{
    /**
     * @var string
     */
    protected $userUuid;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var string
     */
    protected $projectUuid;

    /**
     * @var int
     */
    protected $allowedWarnings = 0;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'sensiolabs_insight';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);
        if (\array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }

        if (\array_key_exists('user_uuid', $options)) {
            $this->userUuid = $options['user_uuid'];
        }

        if (\array_key_exists('auth_token', $options)) {
            $this->authToken = $this->builder->interpolate($options['auth_token'], true);
        }

        if (\array_key_exists('project_uuid', $options)) {
            $this->projectUuid = $options['project_uuid'];
        }

        $this->executable = $this->findBinary('insight');
    }

    /**
     * Runs Sensiolabs Insights in a specified directory.
     *
     * @throws Exception
     */
    public function execute()
    {
        $insightBinaryPath = $this->executable;

        $this->executeSensiolabsInsight($insightBinaryPath);

        $errorCount = $this->processReport(\trim($this->builder->getLastOutput()));
        $this->build->storeMeta((self::pluginName() . '-warnings'), $errorCount);

        return $this->wasLastExecSuccessful($errorCount);
    }

    /**
     * Process PHPMD's XML output report.
     *
     * @return int
     *
     * @throws Exception
     */
    protected function processReport($xmlString)
    {
        $xml = \simplexml_load_string($xmlString);

        if ($xml === false) {
            $this->builder->log($xmlString);

            throw new RuntimeException('Could not process PHPMD report XML.');
        }

        $warnings = 0;

        foreach ($xml->file as $file) {
            $fileName = (string)$file['name'];
            $fileName = \str_replace($this->builder->buildPath, '', $fileName);

            foreach ($file->violation as $violation) {
                $warnings++;

                $this->build->reportError(
                    $this->builder,
                    self::pluginName(),
                    (string)$violation,
                    PHPCensor\Model\BuildError::SEVERITY_HIGH,
                    $fileName,
                    (int)$violation['beginline'],
                    (int)$violation['endline']
                );
            }
        }

        return $warnings;
    }

    /**
     * Execute Sensiolabs Insight.
     */
    protected function executeSensiolabsInsight($binaryPath)
    {
        $cmd = $binaryPath . ' -n analyze --reference %s %s --api-token %s --user-uuid %s';

        // Run Sensiolabs Insight:
        $this->builder->executeCommand(
            $cmd,
            $this->build->getBranch(),
            $this->projectUuid,
            $this->authToken,
            $this->userUuid
        );

        $cmd = $binaryPath . ' -n analysis --format pmd %s --api-token %s --user-uuid %s';

        // Run Sensiolabs Insight:
        $this->builder->executeCommand(
            $cmd,
            $this->projectUuid,
            $this->authToken,
            $this->userUuid
        );
    }

    /**
     * Returns a bool indicating if the error count can be considered a success.
     *
     * @param int $errorCount
     *
     * @return bool
     */
    protected function wasLastExecSuccessful($errorCount)
    {
        if ($this->allowedWarnings !== -1 && $errorCount > $this->allowedWarnings) {
            return false;
        }

        return true;
    }
}
