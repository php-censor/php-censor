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
    protected $allowed_warnings;
    
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

        $this->allowed_warnings = 0;

        if (isset($options['zero_config']) && $options['zero_config']) {
            $this->allowed_warnings = -1;
        }

        if (array_key_exists('allowed_warnings', $options)) {
            $this->allowed_warnings = (int)$options['allowed_warnings'];
        }
    }

    /**
     * Check if this plugin can be executed.
     *
     * @param         $stage
     * @param Builder $builder
     * @param Build   $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        $path = $builder->buildPath . DIRECTORY_SEPARATOR . 'composer.lock';

        if (file_exists($path) && $stage == Build::STAGE_TEST) {
            return true;
        }

        return false;
    }

    public function execute()
    {
        $success   = true;
        $checker   = new BaseSecurityChecker();
        $warnings  = $checker->check($this->builder->buildPath . DIRECTORY_SEPARATOR . 'composer.lock');

        if ($warnings) {
            foreach ($warnings as $library => $warning) {
                foreach ($warning['advisories'] as $advisory => $data) {
                    $this->build->reportError(
                        $this->builder,
                        'security_checker',
                        $library . ' (' . $warning['version'] . ")\n" . $data['cve'] . ': ' . $data['title'] . "\n" . $data['link'],
                        BuildError::SEVERITY_CRITICAL,
                        '-',
                        '-'
                    );
                }
            }

            if ($this->allowed_warnings != -1 && ((int)$checker->getLastVulnerabilityCount() > $this->allowed_warnings)) {
                $success = false;
            }
        }

        return $success;
    }
}
