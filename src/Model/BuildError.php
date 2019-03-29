<?php

namespace PHPCensor\Model;

use PHPCensor\Model\Base\BuildError as BaseBuildError;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\Factory;

class BuildError extends BaseBuildError
{
    /**
     * @return Build|null
     */
    public function getBuild()
    {
        $buildId = $this->getBuildId();
        if (empty($buildId)) {
            return null;
        }

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        return $buildStore->getById($buildId);
    }

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
        return md5($plugin . $file . $lineStart . $lineEnd . $severity . $message);
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
