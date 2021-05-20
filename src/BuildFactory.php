<?php

namespace PHPCensor;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;

/**
 * BuildFactory - Takes in a generic "Build" and returns a type-specific build model.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildFactory
{
    /**
     * @param ConfigurationInterface $configuration
     * @param StoreRegistry          $storeRegistry
     * @param int                    $buildId
     *
     * @return Build|null
     *
     * @throws Exception\HttpException
     */
    public static function getBuildById(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        int $buildId
    ): ?Build {
        $build = $storeRegistry->get('Build')->getById($buildId);

        if (empty($build)) {
            return null;
        }

        return self::getBuild($configuration, $storeRegistry, $build);
    }

    /**
     * Takes a generic build and returns a type-specific build model.
     *
     * @param ConfigurationInterface $configuration
     * @param StoreRegistry          $storeRegistry
     * @param Build                  $build
     *
     * @return Build
     *
     * @throws Exception\HttpException
     */
    public static function getBuild(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        Build $build
    ): Build {
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

            if ($class instanceof Build\GitBuild || $class instanceof Build\HgBuild) {
                $build = new $class($configuration, $storeRegistry, $build->getDataArray());
            } else {
                $build = new $class($configuration, $build->getDataArray());
            }
        }

        return $build;
    }
}
