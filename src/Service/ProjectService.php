<?php

declare(strict_types=1);

namespace PHPCensor\Service;

use DateTime;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectStore;
use Symfony\Component\Filesystem\Filesystem;
use PHPCensor\Common\Exception\InvalidArgumentException;

/**
 * The project service handles the creation, modification and deletion of projects.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectService
{
    private ProjectStore $projectStore;
    private BuildStore $buildStore;
    private EnvironmentStore $environmentStore;

    public function __construct(
        BuildStore $buildStore,
        EnvironmentStore $environmentStore,
        ProjectStore $projectStore
    ) {
        $this->projectStore     = $projectStore;
        $this->buildStore       = $buildStore;
        $this->environmentStore = $environmentStore;
    }

    /**
     * Create a new project model and use the project store to save it.
     */
    public function createProject(string $title, string $type, string $reference, int $userId, array $options = []): Project
    {
        // Create base project and use updateProject() to set its properties:
        $project = new Project($this->buildStore, $this->environmentStore);
        $project->setCreateDate(new DateTime());
        $project->setUserId($userId);

        return $this->updateProject($project, $title, $type, $reference, $options);
    }

    /**
     * Update the properties of a given project.
     *
     * @throws InvalidArgumentException
     */
    public function updateProject(
        Project $project,
        string $title,
        string $type,
        string $reference,
        array $options = []
    ): Project {
        // Set basic properties:
        $project->setTitle($title);
        $project->setType($type);
        $project->setReference($reference);
        $project->setAllowPublicStatus(false);
        $project->setDefaultBranchOnly(false);
        $project->setOverwriteBuildConfig(true);

        // Handle extra project options:
        if (\array_key_exists('ssh_private_key', $options)) {
            $project->setSshPrivateKey($options['ssh_private_key']);
        }

        if (\array_key_exists('ssh_public_key', $options)) {
            $project->setSshPublicKey($options['ssh_public_key']);
        }

        if (\array_key_exists('overwrite_build_config', $options)) {
            $project->setOverwriteBuildConfig($options['overwrite_build_config']);
        }

        if (\array_key_exists('build_config', $options)) {
            $project->setBuildConfig($options['build_config']);
        }

        if (\array_key_exists('allow_public_status', $options)) {
            $project->setAllowPublicStatus($options['allow_public_status']);
        }

        if (\array_key_exists('archived', $options)) {
            $project->setArchived($options['archived']);
        }

        if (\array_key_exists('default_branch', $options)) {
            $project->setDefaultBranch($options['default_branch']);
        }

        if (\array_key_exists('default_branch_only', $options)) {
            $project->setDefaultBranchOnly($options['default_branch_only']);
        }

        if (\array_key_exists('group', $options)) {
            $project->setGroupId((int)$options['group']);
        } else {
            $project->setGroupId(1);
        }

        $project = $this->processAccessInformation($project);

        // Save and return the project:
        /** @var Project $project */
        $project = $this->projectStore->save($project);

        if (\array_key_exists('environments', $options)) {
            $project->setEnvironments($options['environments']);
        }

        return $project;
    }

    /**
     * Delete a given project.
     */
    public function deleteProject(Project $project): bool
    {
        if (!$project->getId()) {
            return false;
        }

        $fileSystem = new Filesystem();

        $fileSystem->remove(RUNTIME_DIR . 'builds/' . $project->getId());
        $fileSystem->remove(PUBLIC_DIR . 'artifacts/pdepend/' . $project->getId());
        $fileSystem->remove(PUBLIC_DIR . 'artifacts/phpunit/' . $project->getId());

        return $this->projectStore->delete($project);
    }

    /**
     * In circumstances where it is necessary, populate access information based on other project properties.
     */
    public function processAccessInformation(Project $project): Project
    {
        $reference = $project->getReference();

        if (\in_array($project->getType(), [
            Project::TYPE_GITHUB,
            Project::TYPE_GITLAB
        ], true)) {
            $info = [];

            if (\preg_match(
                '#^((https|http|ssh)://)?((.+)@)?(([^/:]+):?)(:?([0-9]*)/?)(.+)\.git#',
                $reference,
                $matches
            )) {
                if (isset($matches[4]) && $matches[4]) {
                    $info['user'] = $matches[4];
                }

                if (isset($matches[6]) && $matches[6]) {
                    $info['domain'] = $matches[6];
                }

                if (isset($matches[8]) && $matches[8]) {
                    $info['port'] = $matches[8];
                }

                if (isset($matches[9]) && $matches[9]) {
                    $info['reference'] = $matches[9];

                    $project->setReference($matches[9]);
                }

                $info['origin'] = $reference;
            }

            $project->setAccessInformation($info);
        }

        return $project;
    }
}
