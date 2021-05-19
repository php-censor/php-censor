<?php

namespace PHPCensor\Controller;

use PHPCensor\BuildFactory;
use PHPCensor\Http\Response;
use PHPCensor\Store\BuildStore;
use PHPCensor\View;
use PHPCensor\WebController;

/**
 * Widget Last Builds Controller
 */
class WidgetLastBuildsController extends WebController
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
        parent::init();

        $this->buildStore = $this->storeRegistry->get('Build');
    }

    /**
    * Display dashboard.
    */
    public function index()
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($this->configuration, $build);
        }

        $view = new View('WidgetLastBuilds/update');

        $view->builds         = $builds;
        $this->view->timeline = $view->render();

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    /**
     * @return Response
     */
    public function update()
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($this->configuration, $build);
        }

        $this->view->builds           = $builds;
        $this->view->environmentStore = $this->storeRegistry->get('Environment');

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }
}
