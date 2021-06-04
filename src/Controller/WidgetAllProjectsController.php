<?php

namespace PHPCensor\Controller;

use Exception;
use PHPCensor\Exception\HttpException;
use PHPCensor\Http\Response;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\View;
use PHPCensor\WebController;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WidgetAllProjectsController extends WebController
{
    protected BuildStore $buildStore;

    protected ProjectStore $projectStore;

    protected ProjectGroupStore $groupStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init(): void
    {
        parent::init();

        $this->buildStore   = $this->storeRegistry->get('Build');
        $this->projectStore = $this->storeRegistry->get('Project');
        $this->groupStore   = $this->storeRegistry->get('ProjectGroup');
    }

    /**
     * @return Response
     *
     * @throws Exception
     */
    public function index()
    {
        $this->view->groups = $this->getGroupInfo();

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    /**
     * Generate the HTML for the project overview section of the dashboard.
     *
     * @param Project[] $projects
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getSummaryHtml($projects)
    {
        $summaryBuilds = [];
        $successes     = [];
        $failures      = [];
        $counts        = [];

        foreach ($projects as $project) {
            $summaryBuilds[$project->getId()] = $this->buildStore->getLatestBuilds($project->getId());

            $count = $this->buildStore->getWhere(
                ['project_id' => $project->getId()],
                1,
                0,
                ['id' => 'DESC']
            );
            $counts[$project->getId()] = $count['count'];

            $success = $this->buildStore->getLastBuildByStatus($project->getId(), Build::STATUS_SUCCESS);
            $failure = $this->buildStore->getLastBuildByStatus($project->getId(), Build::STATUS_FAILED);

            $successes[$project->getId()] = $success;
            $failures[$project->getId()]  = $failure;
        }

        $view = new View('WidgetAllProjects/index-projects');

        $view->projects   = $projects;
        $view->builds     = $summaryBuilds;
        $view->successful = $successes;
        $view->failed     = $failures;
        $view->counts     = $counts;

        return $view->render();
    }

    /**
     * Get a summary of the project groups we have, and what projects they have in them.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function getGroupInfo()
    {
        $rtn    = [];
        $groups = $this->groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

        foreach ($groups['items'] as $group) {
            $thisGroup = ['title' => $group->getTitle()];
            $projects  = $this->projectStore->getByGroupId($group->getId(), false);

            $thisGroup['projects'] = $projects['items'];
            $thisGroup['summary']  = $this->getSummaryHtml($thisGroup['projects']);

            $rtn[] = $thisGroup;
        }

        return $rtn;
    }

    /**
     * @param int $projectId
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function update($projectId)
    {
        $count = $this->buildStore->getWhere(
            ['project_id' => $projectId],
            1,
            0,
            ['id' => 'DESC']
        );
        $counts = $count['count'];

        $this->view->project    = $this->projectStore->getById($projectId);
        $this->view->builds     = $this->buildStore->getLatestBuilds($projectId);
        $this->view->successful = $this->buildStore->getLastBuildByStatus($projectId, Build::STATUS_SUCCESS);
        $this->view->failed     = $this->buildStore->getLastBuildByStatus($projectId, Build::STATUS_FAILED);
        $this->view->counts     = $counts;

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }
}
