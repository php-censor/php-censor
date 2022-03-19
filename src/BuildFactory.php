<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * BuildFactory - Takes in a generic Build and returns a type-specific Build model.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildFactory
{
    private ConfigurationInterface $configuration;

    private StoreRegistry $storeRegistry;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry
    ) {
        $this->configuration = $configuration;
        $this->storeRegistry = $storeRegistry;
    }

    /**
     * @throws Common\Exception\RuntimeException
     * @throws Exception\HttpException
     */
    public function getBuildById(int $buildId): ?Build
    {
        /** @var Build $build */
        $build = $this->storeRegistry->get('Build')->getById($buildId);

        if (!$build) {
            return null;
        }

        return $this->getBuild($build);
    }

    /**
     * Takes a generic build and returns a type-specific build model.
     *
     * @throws Exception\HttpException
     */
    public function getBuild(Build $build): Build
    {
        $project = $build->getProject();
        if (!empty($project)) {
            switch ($project->getType()) {
                case Project::TYPE_LOCAL:
                    $type = 'LocalBuild';
                    break;
                case Project::TYPE_GIT:
                    $type = 'GitBuild';
                    break;
                case Project::TYPE_GITHUB:
                    $type = 'GithubBuild';
                    break;
                case Project::TYPE_BITBUCKET:
                    $type = 'BitbucketBuild';
                    break;
                case Project::TYPE_GITLAB:
                    $type = 'GitlabBuild';
                    break;
                case Project::TYPE_GOGS:
                    $type = 'GogsBuild';
                    break;
                case Project::TYPE_HG:
                    $type = 'HgBuild';
                    break;
                case Project::TYPE_BITBUCKET_HG:
                    $type = 'BitbucketHgBuild';
                    break;
                case Project::TYPE_BITBUCKET_SERVER:
                    $type = 'BitbucketServerBuild';
                    break;
                case Project::TYPE_SVN:
                    $type = 'SvnBuild';
                    break;
                default:
                    return $build;
            }

            $class = '\\PHPCensor\\Model\\Build\\' . $type;
            $build = new $class($this->configuration, $this->storeRegistry, $build->getDataArray());
        }

        return $build;
    }
}
