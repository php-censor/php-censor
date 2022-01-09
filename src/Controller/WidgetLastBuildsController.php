<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use PHPCensor\BuildFactory;
use PHPCensor\Http\Response;
use PHPCensor\Store\BuildStore;
use PHPCensor\View;
use PHPCensor\WebController;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WidgetLastBuildsController extends WebController
{
    protected BuildStore $buildStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init(): void
    {
        parent::init();

        $this->buildStore = $this->storeRegistry->get('Build');
    }

    /**
    * Display dashboard.
    */
    public function index(): Response
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($this->configuration, $this->storeRegistry, $build);
        }

        $view = new View('WidgetLastBuilds/update');

        $view->builds         = $builds;
        $this->view->timeline = $view->render();

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    public function update(): Response
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = BuildFactory::getBuild($this->configuration, $this->storeRegistry, $build);
        }

        $this->view->builds           = $builds;
        $this->view->environmentStore = $this->storeRegistry->get('Environment');

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }
}
