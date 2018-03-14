<?php

namespace PHPCensor;

use PHPCensor\Model\Project;
use PHPCensor\Store\Factory;
use PHPCensor\Model\Build;

/**
 * BuildFactory - Takes in a generic "Build" and returns a type-specific build model.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildFactory
{
    /**
     * @param integer $buildId
     *
     * @return Build|null
     */
    public static function getBuildById($buildId)
    {
        $build = Factory::getStore('Build')->getById($buildId);

        if (empty($build)) {
            return null;
        }

        return self::getBuild($build);
    }

    /**
     * Takes a generic build and returns a type-specific build model.
     *
     * @param Build $build The build from which to get a more specific build type.
     *
     * @return Build
     */
    public static function getBuild(Build $build)
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
                case Project::TYPE_SVN:
                    $type = 'SvnBuild';
                    break;
                default:
                    return $build;
            }

            $class = '\\PHPCensor\\Model\\Build\\' . $type;
            $build = new $class($build->getDataArray());
        }

        return $build;
    }
}
