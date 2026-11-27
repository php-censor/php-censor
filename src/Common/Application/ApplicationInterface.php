<?php

declare(strict_types=1);

namespace PHPCensor\Common\Application;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ApplicationInterface
{
    /**
     * Returns system (application) configuration.
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * Example: /var/www/php-censor.localhost/
     */
    public function getRootPath(): string;

    /**
     * Application config option: php-censor.build.allow_public_artifacts
     */
    public function isPublicArtifactsAllowed(): bool;

    /**
     * Example: https://php-censor.localhost/artifacts/
     */
    public function getArtifactsLink(): string;

    /**
     * Example: /var/www/php-censor.localhost/public/artifacts/
     */
    public function getArtifactsPath(): string;
}
