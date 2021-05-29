<?php

namespace PHPCensor\Controller;

use PHPCensor\Exception\HttpException;
use PHPCensor\Http\Response;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\View;
use PHPCensor\WebController;

/**
 * Widget Build Errors Controller
 */
class WidgetBuildErrorsController extends WebController
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
        parent::init();

        $this->buildStore   = $this->storeRegistry->get('Build');
        $this->projectStore = $this->storeRegistry->get('Project');
    }

    /**
    * Display dashboard.
    */
    public function index()
    {
        $view = new View('WidgetBuildErrors/update');

        $this->view->projects = $this->renderAllProjectsLatestBuilds($view);

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    /**
     * @return Response
     *
     * @throws HttpException
     */
    public function update()
    {
        $response = new Response();
        $response->setContent($this->renderAllProjectsLatestBuilds($this->view));

        return $response;
    }

    /**
     * @param View $view
     *
     * @return string
     *
     * @throws HttpException
     */
    protected function renderAllProjectsLatestBuilds($view)
    {
        $builds = $this->buildStore->getAllProjectsLatestBuilds();

        if (!empty($builds['projects'])) {
            $view->builds = $builds['projects'];
            $projects     = $this->projectStore->getByIds(array_keys($builds['projects']));

            $viewProjects = [];
            foreach ($projects as $id => $project) {
                if (!$project->getArchived()) {
                    $viewProjects[$id] = $project;
                } else {
                    unset($builds['projects'][$id]);
                }
            }
            $view->projects = $viewProjects;
        } else {
            $view = new View('WidgetBuildErrors/empty');
        }

        return $view->render();
    }
}
