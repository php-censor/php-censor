<?php

declare(strict_types=1);

namespace PHPCensor\Common\Plugin;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface PathResolverInterface
{
    public function resolveDirectory(string $pluginDirectory): string;

    public function resolveBinaryPath(string $pluginBinaryPath): string;

    public function resolvePath(string $path, bool $isFile = false): string;

    /**
     * @param string[] $pluginIgnores
     *
     * @return string[]
     */
    public function resolveIgnores(array $pluginIgnores, bool $onlyInBuildPath = true): array;
}
