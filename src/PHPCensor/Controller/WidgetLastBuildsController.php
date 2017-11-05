<?php

namespace PHPCensor\Controller;

use b8;
use PHPCensor\BuildFactory;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Controller;

/**
 * Widget Last Builds Controller
 */
class WidgetLastBuildsController extends Controller
{
    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $buildStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore = b8\Store\Factory::getStore('Build');
    }

    /**
    * Display dashboard:
    */
    public function index()
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        $view = new b8\View('WidgetLastBuilds/update');
        $view->builds = $builds;

        $this->view->timeline = $view->render();

        $this->response->disableLayout();
        $this->response->setContent($this->view->render());

        return $this->response;
    }

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
