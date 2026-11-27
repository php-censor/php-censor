<?php

declare(strict_types=1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\ParameterBag;
use PHPCensor\Common\VariableInterpolatorInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class PathResolver implements PathResolverInterface
{
    private BuildInterface $build;

    private BuildLoggerInterface $buildLogger;

    private VariableInterpolatorInterface $variableInterpolator;

    protected ParameterBag $buildSettings;

    private ?string $buildDirectory = null;

    private ?string $buildBinaryPath = null;

    /**
     * @var string[]|null
     */
    private ?array $buildIgnores = null;

    public function __construct(
        BuildInterface $build,
        BuildLoggerInterface $buildLogger,
        VariableInterpolatorInterface $variableInterpolator,
        array $projectConfig = []
    ) {
        $this->build                = $build;
        $this->buildLogger          = $buildLogger;
        $this->variableInterpolator = $variableInterpolator;

        $this->initBuildSettings($projectConfig);
    }

    public function resolveDirectory(string $pluginDirectory): string
    {
        if ($pluginDirectory) {
            $directory = $this->normalizePath($pluginDirectory, $this->build->getBuildPath());
        } else {
            $directory = $this->getBuildDirectory();
        }

        $finalDirectory = $this->getRealPath($directory);

        $this->buildLogger->logDebug(
            \sprintf('Directory: %s', $finalDirectory)
        );

        return $finalDirectory;
    }

    public function resolveBinaryPath(string $pluginBinaryPath): string
    {
        if ($pluginBinaryPath) {
            $binaryPath = $this->normalizePath($pluginBinaryPath, $this->build->getBuildPath());
        } else {
            $binaryPath = $this->getBuildBinaryPath();
        }

        $finalBinaryPath = $this->getRealPath($binaryPath);

        $this->buildLogger->logDebug(
            \sprintf('Binary path: %s', $finalBinaryPath)
        );

        return $finalBinaryPath;
    }

    public function resolvePath(string $path, bool $isFile = false): string
    {
        return $this->getRealPath(
            $this->normalizePath($path, $this->build->getBuildPath()),
            $isFile
        );
    }

    /**
     * {@inheritDoc}
     */
    public function resolveIgnores(array $pluginIgnores, bool $onlyInBuildPath = true): array
    {
        $ignores = $this->getBuildIgnores();
        if ($pluginIgnores) {
            $ignores = \array_merge(
                $ignores,
                \array_filter($pluginIgnores, function ($item) {
                    return !empty($item);
                })
            );
        }

        $baseDirectory = $this->build->getBuildPath();

        \array_walk($ignores, function (string &$value) use ($baseDirectory) {
            $value = \str_replace(
                $baseDirectory,
                '',
                $this->getRealPath(
                    $this->normalizePath($value, $baseDirectory),
                    true
                )
            );
        });

        foreach ($ignores as $index => $ignore) {
            if (
                !$ignore ||
                ($onlyInBuildPath && '/' === \substr($ignore, 0, 1))
            ) {
                unset($ignores[$index]);
            }
        }

        return \array_values(
            \array_unique($ignores)
        );
    }

    private function getRealPath(string $path, bool $isFile = false): string
    {
        $path = \rtrim(
            \preg_replace(
                '#[/]{2,}#',
                '/',
                \str_replace(['/', '\\'], '/', $path)
            ),
            '/'
        );
        $parts         = \explode('/', $path);
        $absoluteParts = [];
        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }

            if ('..' === $part) {
                \array_pop($absoluteParts);
            } else {
                $absoluteParts[] = $part;
            }
        }

        $resolvedPath = \implode('/', $absoluteParts);
        if (!$isFile) {
            $resolvedPath .= '/';
        }

        return  $resolvedPath;
    }

    private function normalizePath(string $path, string $basePath): string
    {
        $path = $this->variableInterpolator->interpolate($path);

        if (\in_array(\substr($path, 0, 2), ['/.', '\\.'], true)) {
            $path = \ltrim($path, '\\/');
        }

        if (!\in_array(\substr($path, 0, 1), ['/', '\\'], true)) {
            $path = $basePath . $path;
        }

        return $path;
    }

    private function initBuildSettings(array $projectConfig): void
    {
        $buildSettingArray = [];
        if (!empty($projectConfig['build_settings'])) {
            $buildSettingArray = $projectConfig['build_settings'];
        }

        $this->buildSettings = new ParameterBag($buildSettingArray);
    }

    private function getBuildDirectory(): string
    {
        if (null === $this->buildDirectory) {
            $buildDirectory         = $this->build->getBuildPath();
            /** @var string $buildSettingsDirectory */
            $buildSettingsDirectory = $this->buildSettings->get('directory', '');

            if ($buildSettingsDirectory) {
                $buildDirectory = $this->normalizePath($buildSettingsDirectory, $this->build->getBuildPath());
            }

            $this->buildDirectory = $this->getRealPath($buildDirectory);
        }

        return $this->buildDirectory;
    }

    private function getBuildBinaryPath(): string
    {
        if (null === $this->buildBinaryPath) {
            $buildBinaryPath         = $this->build->getBuildPath();
            /** @var string $buildSettingsBinaryPath */
            $buildSettingsBinaryPath = $this->buildSettings->get('binary_path', '');

            if ($buildSettingsBinaryPath) {
                $buildBinaryPath = $this->normalizePath($buildSettingsBinaryPath, $this->build->getBuildPath());
            }

            $this->buildBinaryPath = $this->getRealPath($buildBinaryPath);
        }

        return $this->buildBinaryPath;
    }

    private function getBuildIgnores(): array
    {
        if (null === $this->buildIgnores) {
            $this->buildIgnores   = [];
            $buildSettingsIgnores = $this->buildSettings->get('ignore', []);

            if ($buildSettingsIgnores) {
                $this->buildIgnores = \array_filter(
                    $buildSettingsIgnores,
                    function (string $item) {
                        return !empty($item);
                    }
                );
            }
        }

        return $this->buildIgnores;
    }
}
