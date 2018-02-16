<?php

namespace PHPCensor\Controller;

use b8\Store\Factory;
use PHPCensor\View;
use b8\Http\Response;
use PHPCensor\BuildFactory;
use PHPCensor\Controller;
use PHPCensor\Store\BuildStore;

/**
 * Widget Last Builds Controller
 */
class WidgetLastBuildsController extends Controller
{
    /**
     * @var BuildStore
     */
    protected $buildStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore = Factory::getStore('Build');
    }

    /**
    * Display dashboard.
    */
    public function index()
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        $view = new View('WidgetLastBuilds/update');

        $view->builds         = $builds;
        $this->view->timeline = $view->render();

        $this->response->disableLayout();
        $this->response->setContent($this->view->render());

        return $this->response;
    }

    /**
     * @return Response
     */
    public function update()
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        $this->view->builds = $builds;

        $this->response->disableLayout();
        $this->response->setContent($this->view->render());

        return $this->response;
    }
}
