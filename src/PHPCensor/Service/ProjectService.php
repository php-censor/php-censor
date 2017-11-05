<?php

namespace PHPCensor\Service;

use PHPCensor\Model\Project;
use PHPCensor\Store\ProjectStore;

/**
 * The project service handles the creation, modification and deletion of projects.
 */
class ProjectService
{
    /**
     * @var \PHPCensor\Store\ProjectStore
     */
    protected $projectStore;

    /**
     * @param ProjectStore $projectStore
     */
    public function __construct(ProjectStore $projectStore)
    {
        $this->projectStore = $projectStore;
    }

    /**
     * Create a new project model and use the project store to save it.
     *
     * @param string  $title
     * @param string  $type
     * @param string  $reference
     * @param integer $userId
     * @param array   $options
     *
     * @return \PHPCensor\Model\Project
     */
    public function createProject($title, $type, $reference, $userId, $options = [])
    {
        // Create base project and use updateProject() to set its properties:
        $project = new Project();
        $project->setCreateDate(new \DateTime());
        $project->setUserId((integer)$userId);

        return $this->updateProject($project, $title, $type, $reference, $options);
    }

    /**
     * Update the properties of a given project.
     *
     * @param Project $project
     * @param string $title
     * @param string $type
     * @param string $reference
     * @param array $options
     *
     * @return \PHPCensor\Model\Project
     */
    public function updateProject(Project $project, $title, $type, $reference, $options = [])
    {
        // Set basic properties:
        $project->setTitle($title);
        $project->setType($type);
        $project->setReference($reference);
        $project->setAllowPublicStatus(0);
        $project->setDefaultBranchOnly(0);

        // Handle extra project options:
        if (array_key_exists('ssh_private_key', $options)) {
            $project->setSshPrivateKey($options['ssh_private_key']);
        }

        if (array_key_exists('ssh_public_key', $options)) {
            $project->setSshPublicKey($options['ssh_public_key']);
        }

        if (array_key_exists('build_config', $options)) {
            $project->setBuildConfig($options['build_config']);
        }

        if (array_key_exists('allow_public_status', $options)) {
            $project->setAllowPublicStatus((int)$options['allow_public_status']);
        }

        if (array_key_exists('archived', $options)) {
            $project->setArchived((bool)$options['archived']);
        }

        if (array_key_exists('branch', $options)) {
            $project->setBranch($options['branch']);
        }

        if (array_key_exists('default_branch_only', $options)) {
            $project->setDefaultBranchOnly((int)$options['default_branch_only']);
        }

        if (array_key_exists('group', $options)) {
            $project->setGroup($options['group']);
        }

        // Allow certain project types to set access information:
        $this->processAccessInformation($project);

        // Save and return the project:
        /** @var Project $project */
        $project = $this->projectStore->save($project);

        if (array_key_exists('environments', $options)) {
            $project->setEnvironments($options['environments']);
        }

        return $project;
    }

    /**
     * Delete a given project.
     *
     * @param Project $project
     *
     * @return bool
     */
    public function deleteProject(Project $project)
    {
        return $this->projectStore->delete($project);
    }

    /**
     * In circumstances where it is necessary, populate access information based on other project properties.
     *
     * @see ProjectService::createProject()
     *
     * @param Project $project
     */
    protected function processAccessInformation(Project &$project)
    {
        $matches   = [];
        $reference = $project->getReference();

        if ($project->getType() == 'gitlab') {
            $info = [];

            if (preg_match('`^(.+)@(.+):([0-9]*)\/?(.+)\.git`', $reference, $matches)) {
                $info['user'] = $matches[1];
                $info['domain'] = $matches[2];
                $info['port'] = $matches[3];

                $project->setReference($matches[4]);
            }

            $project->setAccessInformation($info);
        }
    }
}
