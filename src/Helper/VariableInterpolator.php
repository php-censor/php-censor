<?php

declare(strict_types=1);

namespace PHPCensor\Helper;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Project\ProjectInterface;
use PHPCensor\Common\Repository\EnvironmentRepositoryInterface;
use PHPCensor\Common\Repository\SecretRepositoryInterface;
use PHPCensor\Common\VariableInterpolatorInterface;

/**
 * The BuildInterpolator class replaces variables in a string with build-specific information.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class VariableInterpolator implements VariableInterpolatorInterface
{
    /**
     * An array of key => value pairs that will be used for
     * interpolation and environment variables
     *
     * @var array<string, string>
     */
    private array $variables = [];

    private BuildInterface $build;
    private ProjectInterface $project;
    private EnvironmentRepositoryInterface $environmentStore;
    private SecretRepositoryInterface $secretStore;

    private string $applicationVersion;

    public function __construct(
        BuildInterface $build,
        ProjectInterface $project,
        EnvironmentRepositoryInterface $environmentStore,
        SecretRepositoryInterface $secretStore,
        string $applicationVersion
    ) {
        $this->build              = $build;
        $this->project            = $project;
        $this->environmentStore   = $environmentStore;
        $this->secretStore        = $secretStore;
        $this->applicationVersion = $applicationVersion;

        $this->initVariables();
        $this->initEnvironmentVariables();
    }

    private function getEnvironmentName(?int $environmentId): string
    {
        $environmentName = '';
        $environment     = $this->environmentStore->getOneById((int)$environmentId);
        if ($environment) {
            $environmentName = (string)$environment->getName();
        }

        return $environmentName;
    }

    private function initVariables(): void {
        $this->variables = [
            '%COMMIT_ID%'       => $this->build->getCommitId(),
            '%SHORT_COMMIT_ID%' => \substr((string)$this->build->getCommitId(), 0, 7),
            '%COMMITTER_EMAIL%' => $this->build->getCommitterEmail(),
            '%COMMIT_MESSAGE%'  => $this->build->getCommitMessage(),
            '%COMMIT_LINK%'     => $this->build->getCommitLink(),
            '%PROJECT_ID%'      => (string)$this->project->getId(),
            '%PROJECT_TITLE%'   => $this->project->getTitle(),
            '%PROJECT_LINK%'    => $this->project->getLink(),
            '%BUILD_ID%'        => (string)$this->build->getId(),
            '%BUILD_PATH%'      => $this->build->getBuildPath(),
            '%BUILD_LINK%'      => $this->build->getLink(),
            '%BRANCH%'          => $this->build->getBranch(),
            '%BRANCH_LINK%'     => $this->build->getBranchLink(),
            '%ENVIRONMENT%'     => $this->getEnvironmentName($this->build->getEnvironmentId()),
            '%SYSTEM_VERSION%'  => $this->applicationVersion,
        ];
    }

    private function initEnvironmentVariables(): void
    {
        \putenv('PHP_CENSOR=1');
        \putenv('PHP_CENSOR_COMMIT_ID=' . $this->variables['%COMMIT_ID%']);
        \putenv('PHP_CENSOR_SHORT_COMMIT_ID=' . $this->variables['%SHORT_COMMIT_ID%']);
        \putenv('PHP_CENSOR_COMMITTER_EMAIL=' . $this->variables['%COMMITTER_EMAIL%']);
        \putenv('PHP_CENSOR_COMMIT_MESSAGE=' . $this->variables['%COMMIT_MESSAGE%']);
        \putenv('PHP_CENSOR_COMMIT_LINK=' . $this->variables['%COMMIT_LINK%']);
        \putenv('PHP_CENSOR_PROJECT_ID=' . $this->variables['%PROJECT_ID%']);
        \putenv('PHP_CENSOR_PROJECT_TITLE=' . $this->variables['%PROJECT_TITLE%']);
        \putenv('PHP_CENSOR_PROJECT_LINK=' . $this->variables['%PROJECT_LINK%']);
        \putenv('PHP_CENSOR_BUILD_ID=' . $this->variables['%BUILD_ID%']);
        \putenv('PHP_CENSOR_BUILD_PATH=' . $this->variables['%BUILD_PATH%']);
        \putenv('PHP_CENSOR_BUILD_LINK=' . $this->variables['%BUILD_LINK%']);
        \putenv('PHP_CENSOR_BRANCH=' . $this->variables['%BRANCH%']);
        \putenv('PHP_CENSOR_BRANCH_LINK=' . $this->variables['%BRANCH_LINK%']);
        \putenv('PHP_CENSOR_ENVIRONMENT=' . $this->variables['%ENVIRONMENT%']);
        \putenv('PHP_CENSOR_SYSTEM_VERSION=' . $this->variables['%SYSTEM_VERSION%']);
    }

    private function realtimeInterpolate(string $string): string
    {
        return \str_replace(
            ['%CURRENT_DATE%', '%CURRENT_TIME%', '%CURRENT_DATETIME%'],
            [\date('Y-m-d'), \date('H-i-s'), \date('Y-m-d_H-i-s')],
            $string
        );
    }

    private function secretInterpolate(string $input): string
    {
        \preg_match_all('#%SECRET:(.+?)%#', $input, $matches);
        if (!empty($matches[0])) {
            $secrets = $this->secretStore->getByNames($matches[1]);
            $finalSecrets = [];

            foreach ($matches[0] as $index => $match) {
                $finalSecrets[$index] = $secrets[$matches[1][$index]]->getValue();
            }

            $input = \str_replace($matches[0], $finalSecrets, $input);
        }

        return $input;
    }

    public function interpolate(string $string): string
    {
        $string = $this->secretInterpolate($string);
        $string = $this->realtimeInterpolate($string);

        $keys   = \array_keys($this->variables);
        $values = \array_values($this->variables);

        return \str_replace($keys, $values, $string);
    }
}
