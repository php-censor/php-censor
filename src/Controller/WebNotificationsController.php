<?php

namespace PHPCensor\Controller;

use PHPCensor\Model\Build;
use PHPCensor\Store\BuildStore;
use PHPCensor\WebController;
use PHPCensor\Store\Factory;
use PHPCensor\Http\Response\JsonResponse;
use PHPCensor\Service\WebNotificationService;

/**
 * Web Notifications Controller
 */
class WebNotificationsController extends WebController
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

        $this->buildStore = Factory::getStore('Build');
    }

    /**
     * Provides JSON format for web notification UI of all last builds that have success and failed status.
     * This is similar to WidgetAllProjectsController::update() but instead, this only returns JSON.
     *
     * @param  int $projectId
     *
     * @return \PHPCensor\Http\Response\JsonResponse
     *
     * @see  \PHPCensor\Controller\WidgetAllProjectsController
     */
    public function widgetsAllProjectsUpdate($projectId)
    {
        $success  = $this->buildStore->getLastBuildByStatus($projectId, Build::STATUS_SUCCESS);
        $failed   = $this->buildStore->getLastBuildByStatus($projectId, Build::STATUS_FAILED);

        $oSuccess = WebNotificationService::formatBuild($success);
        $oFailed  = WebNotificationService::formatBuild($failed);

        $aSuccess = [
            'count' => count($oSuccess),
            'items' => [$projectId => ['build' => $oSuccess]]
        ];
        $aFailed  = [
            'count' => count($oFailed),
            'items' => [$projectId => ['build' => $oFailed]]
        ];

        $builds  = [
            'success' => $aSuccess,
            'failed'  => $aFailed
        ];

        $response = new JsonResponse();
        $response->setContent($builds);

        return $response;
    }


    /**
     * Provides JSON format for web notification UI of all last builds that have pending and running status.
     * This is similar to WidgetAllProjectsController::update() but instead, this only returns JSON.
     *
     * @return JsonResponse
     *
     * @throws \PHPCensor\Exception\HttpException
     */
    public function buildsUpdated()
    {
        $pending = $this->buildStore->getByStatus(Build::STATUS_PENDING);
        $running = $this->buildStore->getByStatus(Build::STATUS_RUNNING);

        $result = [
            'pending' => WebNotificationService::formatBuilds($pending),
            'running' => WebNotificationService::formatBuilds($running)
        ];

        $response = new JsonResponse();
        $response->setContent($result);

        return $response;
    }
}
