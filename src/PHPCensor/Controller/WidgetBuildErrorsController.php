<?php

namespace PHPCensor\Controller;

use b8;
use PHPCensor\BuildFactory;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Controller;

/**
 * Widget Build Errors Controller
 */
class WidgetBuildErrorsController extends Controller
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
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore = b8\Store\Factory::getStore('Build');
        $this->projectStore = b8\Store\Factory::getStore('Project');
    }

    /**
    * Display dashboard:
    */
    public function index()
    {
        $view = new b8\View('WidgetBuildErrors/update');
        $this->view->projects = $this->renderAllProjectsLatestBuilds($view);

        $this->response->disableLayout();
        $this->response->setContent($this->view->render());

        return $this->response;
    }

    /**
     * @return b8\Http\Response
     */
    public function update()
    {
        $this->response->disableLayout();
        $this->response->setContent($this->renderAllProjectsLatestBuilds($this->view));

        return $this->response;
    }

    /**
     * @param b8\View $view
     * @return string
     */
    protected function renderAllProjectsLatestBuilds($view)
    {
        $builds = $this->buildStore->getAllProjectsLatestBuilds();

        $view->builds = $builds['projects'];
        $projects = $this->projectStore->getByIds(array_keys($builds['projects']));

        $view_projects = [];
        foreach($projects as $id => $project) {
            if (!$project->getArchived()) {
                $view_projects[$id] = $project;
            } else {
                unset($builds['projects'][$id]);
            }
        }
        $view->projects = $view_projects;

        return $view->render();
    }
}
