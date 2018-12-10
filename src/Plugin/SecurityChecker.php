<?php

namespace PHPCensor\Plugin;

use PHPCensor;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPCensor\Model\BuildError;
use PHPCensor\ZeroConfigPluginInterface;
use SensioLabs\Security\SecurityChecker as BaseSecurityChecker;

/**
 * SensioLabs Security Checker Plugin
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SecurityChecker extends Plugin implements ZeroConfigPluginInterface
{
    /**
     * @var integer
     */
    protected $allowedWarnings;

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

        if (array_key_exists('allowed_warnings', $options)) {
            $this->allowedWarnings = (int)$options['allowed_warnings'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function canExecuteOnStage($stage, Build $build)
    {
        $path = $build->getBuildPath() . 'composer.lock';

        if (file_exists($path) && $stage == Build::STAGE_TEST) {
            return true;
        }

        return false;
    }

    public function execute()
    {
        $success  = true;
        $checker  = new BaseSecurityChecker();
        $result   = $checker->check($this->builder->buildPath . 'composer.lock');
        $warnings = json_decode((string)$result, true);

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

            if ($this->allowedWarnings != -1 && ($result->count() > $this->allowedWarnings)) {
                $success = false;
            }
        }

        return $success;
    }
}
