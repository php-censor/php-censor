<?php

namespace PHPCensor;

use b8\Store\Factory;
use PHPCensor\Model\Build;

/**
 * BuildFactory - Takes in a generic "Build" and returns a type-specific build model.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildFactory
{
    /**
     * @param $buildId
     *
     * @throws \Exception
     *
     * @return Build
     */
    public static function getBuildById($buildId)
    {
        $build = Factory::getStore('Build')->getById($buildId);

        if (empty($build)) {
            throw new \Exception('Build ID ' . $buildId . ' does not exist.');
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
                case 'local':
                    $type = 'LocalBuild';
                    break;
                case 'git':
                    $type = 'GitBuild';
                    break;
                case 'github':
                    $type = 'GithubBuild';
                    break;
                case 'bitbucket':
                    $type = 'BitbucketBuild';
                    break;
                case 'gitlab':
                    $type = 'GitlabBuild';
                    break;
                case 'gogs':
                    $type = 'GogsBuild';
                    break;
                case 'hg':
                    $type = 'HgBuild';
                    break;
                case 'bitbucket-hg':
                    $type = 'BitbucketHgBuild';
                    break;
                case 'svn':
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
