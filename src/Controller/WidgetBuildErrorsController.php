<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use PHPCensor\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\View;
use PHPCensor\WebController;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WidgetBuildErrorsController extends WebController
{
    protected BuildStore $buildStore;

    protected ProjectStore $projectStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init(): void
    {
        parent::init();

        $this->buildStore   = $this->storeRegistry->get('Build');
        $this->projectStore = $this->storeRegistry->get('Project');
    }

    /**
    * Display dashboard.
    */
    public function index(): Response
    {
        $view = new View('WidgetBuildErrors/update');

        $this->view->projects = $this->renderAllProjectsLatestBuilds($view);

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    /**
     * @throws HttpException
     */
    public function update(): Response
    {
        $response = new Response();
        $response->setContent($this->renderAllProjectsLatestBuilds($this->view));

        return $response;
    }

    /**
     * @throws HttpException
     */
    protected function renderAllProjectsLatestBuilds(View $view): string
    {
        $builds = $this->buildStore->getAllProjectsLatestBuilds();

        if (!empty($builds['projects'])) {
            $view->builds = $builds['projects'];
            $projects     = $this->projectStore->getByIds(\array_keys($builds['projects']));

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
