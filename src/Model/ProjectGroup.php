<?php

declare(strict_types=1);

namespace PHPCensor\Model;

use PHPCensor\Model\Base\ProjectGroup as BaseProjectGroup;
use PHPCensor\Store\ProjectStore;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectGroup extends BaseProjectGroup
{
    /**
     * @return Project[]
     */
    public function getGroupProjects()
    {
        /** @var ProjectStore $projectStore */
        $projectStore = $this->storeRegistry->get('Project');

        return $projectStore->getByGroupId($this->getId(), false);
    }
}
