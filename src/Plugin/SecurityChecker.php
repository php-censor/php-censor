<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin;
use PHPCensor\ZeroConfigPluginInterface;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * SensioLabs Security Checker Plugin
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SecurityChecker extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var int
     */
    protected $allowedWarnings;

    /**
     * @var string
     */
    protected $binaryType = 'symfony';

    /**
     * @var string[]
     */
    protected $allowedBinaryTypes = [
        'symfony',
        'local-php-security-checker',
    ];

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'security_checker';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->allowedWarnings = 0;

        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowedWarnings = -1;
        }

        if (\array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }

        if (
            \array_key_exists('binary_type', $options) &&
            \in_array((string)$options['binary_type'], $this->allowedBinaryTypes, true)
        ) {
            $this->binaryType = (string)$options['binary_type'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        $path = $build->getBuildPath() . 'composer.lock';

        if (file_exists($path) && $stage === Build::STAGE_TEST) {
            return true;
        }

        return false;
    }

    public function execute()
    {
        $composerLockFile = $this->builder->buildPath . 'composer.lock';
        if (!\is_file($composerLockFile)) {
            throw new RuntimeException('Lock file does not exist.');
        }

        if ('symfony' === $this->binaryType) {
            $cmd = '%s check:security --format=json --dir=%s';
            $executable = $this->findBinary('symfony');
        } else {
            $cmd = '%s --format=json --path="%s"';
            $executable = $this->findBinary('local-php-security-checker');
        }

        $builder = $this->getBuilder();
        if (!$this->getBuild()->isDebug()) {
            $builder->logExecOutput(false);
        }

        // works with dir, composer.lock, composer.json
        $builder->executeCommand($cmd, $executable, $composerLockFile);

        $builder->logExecOutput(true);

        $success  = true;
        $result   = (string)$builder->getLastOutput();
        $warnings = \json_decode($result, true);

        if ($warnings) {
            foreach ($warnings as $library => $warning) {
                foreach ($warning['advisories'] as $data) {
                    $this->build->reportError(
                        $this->builder,
                        self::pluginName(),
                        $library . ' (' . $warning['version'] . ")\n" . $data['cve'] . ': ' . $data['title'] . "\n" . $data['link'],
                        BuildError::SEVERITY_CRITICAL,
                        '-',
                        '-'
                    );
                }
            }

            if ($this->allowedWarnings != -1 && (\count($warnings) > $this->allowedWarnings)) {
                $success = false;
            }
        } elseif (null === $warnings && $result) {
            throw new RuntimeException('invalid json: '.$result);
        }

        return $success;
    }
}
