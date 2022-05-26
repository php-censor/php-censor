<?php

declare(strict_types=1);

namespace PHPCensor\Model;

use PHPCensor\Model\Base\BuildError as BaseBuildError;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildError extends BaseBuildError
{
    /**
     * Get the language string key for this error's severity level.
     *
     * @return string
     */
    public function getSeverityString()
    {
        switch ($this->getSeverity()) {
            case self::SEVERITY_CRITICAL:
                return 'critical';

            case self::SEVERITY_HIGH:
                return 'high';

            case self::SEVERITY_NORMAL:
                return 'normal';

            case self::SEVERITY_LOW:
                return 'low';
        }
    }

    /**
     * Get the language string key for this error's severity level.
     *
     * @param int $severity
     *
     * @return string
     */
    public static function getSeverityName($severity)
    {
        switch ($severity) {
            case self::SEVERITY_CRITICAL:
                return 'critical';

            case self::SEVERITY_HIGH:
                return 'high';

            case self::SEVERITY_NORMAL:
                return 'normal';

            case self::SEVERITY_LOW:
                return 'low';
        }
    }

    /**
     * @param string  $plugin
     * @param string  $file
     * @param int $lineStart
     * @param int $lineEnd
     * @param int $severity
     * @param string  $message
     *
     * @return string
     */
    public static function generateHash($plugin, $file, $lineStart, $lineEnd, $severity, $message)
    {
        return \md5($plugin . $file . $lineStart . $lineEnd . $severity . $message);
    }

    /**
     * Get the class to apply to HTML elements representing this error.
     *
     * @return string
     */
    public function getSeverityClass()
    {
        switch ($this->getSeverity()) {
            case self::SEVERITY_CRITICAL:
                return 'danger';

            case self::SEVERITY_HIGH:
                return 'warning';

            case self::SEVERITY_NORMAL:
                return 'info';

            case self::SEVERITY_LOW:
                return 'default';
        }
    }
}
