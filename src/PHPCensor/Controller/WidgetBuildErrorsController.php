<?php

namespace PHPCensor\Controller;

use b8\Store\Factory;
use PHPCensor\View;
use b8\Http\Response;
use PHPCensor\Controller;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;

/**
 * Widget Build Errors Controller
 */
class WidgetBuildErrorsController extends Controller
{
    /**
     * @var BuildStore
     */
    protected $buildStore;

    /**
     * @var ProjectStore
     */
    protected $projectStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore   = Factory::getStore('Build');
        $this->projectStore = Factory::getStore('Project');
    }

    /**
    * Display dashboard.
    */
    public function index()
    {
        $view = new View('WidgetBuildErrors/update');

        $this->view->projects = $this->renderAllProjectsLatestBuilds($view);

        $this->response->disableLayout();
        $this->response->setContent($this->view->render());

        return $this->response;
    }

    /**
     * @return Response
     */
    public function update()
    {
        $this->response->disableLayout();
        $this->response->setContent($this->renderAllProjectsLatestBuilds($this->view));

        return $this->response;
    }

    /**
     * @param View $view
     *
     * @return string
     */
    protected function renderAllProjectsLatestBuilds($view)
    {
        $builds = $this->buildStore->getAllProjectsLatestBuilds();

        if (!empty($builds['projects'])) {
            $view->builds = $builds['projects'];
            $projects     = $this->projectStore->getByIds(array_keys($builds['projects']));

            $view_projects = [];
            foreach($projects as $id => $project) {
                if (!$project->getArchived()) {
                    $view_projects[$id] = $project;
                } else {
                    unset($builds['projects'][$id]);
                }
            }
            $view->projects = $view_projects;
        } else {
            $view = new View('WidgetBuildErrors/empty');
        }

        return $view->render();
    }
}
