<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;

/**
 * BuildFactory - Takes in a generic "Build" and returns a type-specific build model.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildFactory
{
    /**
     * @throws Common\Exception\RuntimeException
     * @throws Exception\HttpException
     */
    public static function getBuildById(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        int $buildId
    ): ?Build {
        /** @var Build $build */
        $build = $storeRegistry->get('Build')->getById($buildId);

        if (empty($build)) {
            return null;
        }

        return self::getBuild($configuration, $storeRegistry, $build);
    }

    /**
     * Takes a generic build and returns a type-specific build model.
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
            $build = new $class($configuration, $storeRegistry, $build->getDataArray());
        }

        return $build;
    }
}
