<?php

namespace PHPCensor\Model;

use PHPCensor\Model\Base\ProjectGroup as BaseProjectGroup;
use PHPCensor\Store\Factory;
use PHPCensor\Store\ProjectStore;

class ProjectGroup extends BaseProjectGroup
{
    /**
     * @return Project[]
     */
    public function getGroupProjects()
    {
        /** @var ProjectStore $projectStore */
        $projectStore = Factory::getStore('Project');

        return $projectStore->getByGroupId($this->getId(), false);
    }
}
