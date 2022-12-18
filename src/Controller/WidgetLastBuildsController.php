<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use PHPCensor\BuildFactory;
use Symfony\Component\HttpFoundation\Response;
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

    protected BuildFactory $buildFactory;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init(): void
    {
        parent::init();

        $this->buildFactory = new BuildFactory(
            $this->configuration,
            $this->storeRegistry,
            $this->buildStore
        );
    }

    /**
    * Display dashboard.
    */
    public function index(): Response
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = $this->buildFactory->getBuild($build);
        }

        $view = new View('WidgetLastBuilds/update');

        $view->builds = $builds;
        $view->environmentStore = $this->environmentStore;
        $this->view->timeline = $view->render();

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }

    public function update(): Response
    {
        $builds = $this->buildStore->getLatestBuilds(null, 10);

        foreach ($builds as &$build) {
            $build = $this->buildFactory->getBuild($build);
        }

        $this->view->builds           = $builds;
        $this->view->environmentStore = $this->environmentStore;

        $response = new Response();
        $response->setContent($this->view->render());

        return $response;
    }
}
