<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Controller;

use b8;
use PHPCensor\BuildFactory;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Controller;

/**
* Home Controller - Displays the PHPCI Dashboard.
* @author       Dan Cryer <dan@block8.co.uk>
* @package      PHPCI
* @subpackage   Web
*/
class HomeController extends Controller
{
    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $buildStore;

    /**
     * @var \PHPCensor\Store\ProjectStore
     */
    protected $projectStore;

    /**
     * @var \PHPCensor\Store\ProjectGroupStore
     */
    protected $groupStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore    = b8\Store\Factory::getStore('Build');
        $this->projectStore  = b8\Store\Factory::getStore('Project');
        $this->groupStore    = b8\Store\Factory::getStore('ProjectGroup');
    }

    /**
    * Display PHPCI dashboard:
    */
    public function index()
    {
        $this->layout->title = Lang::get('dashboard');
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        $this->view->builds = $builds;
        $this->view->groups = $this->getGroupInfo();

        return $this->view->render();
    }

    /**
     * Generate the HTML for the project overview section of the dashboard.
     * @param $projects
     * @return string
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
                [],
                ['id' => 'DESC']
            );
            $counts[$project->getId()] = $count['count'];

            $success = $this->buildStore->getLastBuildByStatus($project->getId(), Build::STATUS_SUCCESS);
            $failure = $this->buildStore->getLastBuildByStatus($project->getId(), Build::STATUS_FAILED);

            $successes[$project->getId()] = $success;
            $failures[$project->getId()] = $failure;
        }

        $view = new b8\View('Home/dashboard-projects');
        $view->projects   = $projects;
        $view->builds     = $summaryBuilds;
        $view->successful = $successes;
        $view->failed     = $failures;
        $view->counts     = $counts;

        return $view->render();
    }

    /**
     * Get a summary of the project groups we have, and what projects they have in them.
     * @return array
     */
    protected function getGroupInfo()
    {
        $rtn    = [];
        $groups = $this->groupStore->getWhere([], 100, 0, [], ['title' => 'ASC']);

        foreach ($groups['items'] as $group) {
            $thisGroup             = ['title' => $group->getTitle()];
            $projects              = $this->projectStore->getByGroupId($group->getId());
            $thisGroup['projects'] = $projects['items'];
            $thisGroup['summary']  = $this->getSummaryHtml($thisGroup['projects']);
            $rtn[]                 = $thisGroup;
        }

        return $rtn;
    }
}
