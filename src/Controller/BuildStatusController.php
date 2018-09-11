<?php

namespace PHPCensor\Controller;

use PHPCensor\Http\Response;
use PHPCensor\Http\Response\RedirectResponse;
use PHPCensor\Exception\HttpException\NotFoundException;
use PHPCensor\Store\Factory;
use PHPCensor\BuildFactory;
use PHPCensor\Model\Project;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildStatusService;
use PHPCensor\WebController;

/**
 * Build Status Controller - Allows external access to build status information / images.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildStatusController extends WebController
{
    /**
     * @var string
     */
    public $layoutName = 'layoutPublic';

    /**
     * @var \PHPCensor\Store\ProjectStore
     */
    protected $projectStore;

    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $buildStore;

    public function init()
    {
        parent::init();

        $this->buildStore   = Factory::getStore('Build');
        $this->projectStore = Factory::getStore('Project');
    }

    /**
     * Returns status of the last build
     *
     * @param $projectId
     *
     * @return string
     */
    protected function getStatus($projectId)
    {
        $status = null;
        $branch = $this->getParam('branch', 'master');

        try {
            $project = $this->projectStore->getById($projectId);
            $status = 'passing';

            if (isset($project) && $project instanceof Project) {
                $build = $project->getLatestBuild($branch, [
                    Build::STATUS_SUCCESS,
                    Build::STATUS_FAILED,
                ]);

                if (isset($build) && $build instanceof Build && $build->getStatus() !== Build::STATUS_SUCCESS) {
                    $status = 'failed';
                }
            }
        } catch (\Exception $e) {
            $status = 'error';
        }

        return $status;
    }

    /**
     * Displays projects information in ccmenu format
     *
     * @param $projectId
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function ccxml($projectId)
    {
        /* @var Project $project */
        $project = $this->projectStore->getById($projectId);
        $xml     = new \SimpleXMLElement('<Projects/>');

        if (!$project instanceof Project || !$project->getAllowPublicStatus()) {
            return $this->renderXml($xml);
        }

        try {
            $branchList = $this->buildStore->getBuildBranches($projectId);

            if (!$branchList) {
                $branchList = [$project->getBranch()];
            }

            foreach ($branchList as $branch) {
                $buildStatusService = new BuildStatusService($branch, $project, $project->getLatestBuild($branch));
                if ($attributes = $buildStatusService->toArray()) {
                    $projectXml = $xml->addChild('Project');
                    foreach ($attributes as $attributeKey => $attributeValue) {
                        $projectXml->addAttribute($attributeKey, $attributeValue);
                    }
                }
            }
        } catch (\Exception $e) {
            $xml = new \SimpleXMLElement('<projects/>');
        }

        return $this->renderXml($xml);
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    protected function renderXml(\SimpleXMLElement $xml = null)
    {
        $response = new Response();

        $response->setHeader('Content-Type', 'text/xml');
        $response->setContent($xml->asXML());

        return $response;
    }

    /**
     * Returns the appropriate build status image in SVG format for a given project.
     *
     * @param $projectId
     *
     * @return Response
     */
    public function image($projectId)
    {
        // plastic|flat|flat-squared|social
        $style = $this->getParam('style', 'flat');
        $label = $this->getParam('label', 'build');

        $optionalParams = [
            'logo'      => $this->getParam('logo'),
            'logoWidth' => $this->getParam('logoWidth'),
            'link'      => $this->getParam('link'),
            'maxAge'    => $this->getParam('maxAge'),
        ];

        $status = $this->getStatus($projectId);

        if (is_null($status)) {
            $response = new RedirectResponse();
            $response->setHeader('Location', '/');

            return $response;
        }

        $color    = ($status == 'passing') ? 'green' : 'red';
        $imageUrl = sprintf(
            'http://img.shields.io/badge/%s-%s-%s.svg?style=%s',
            $label,
            $status,
            $color,
            $style
        );

        foreach ($optionalParams as $paramName => $param) {
            if ($param) {
                $imageUrl .= '&' . $paramName . '=' . $param;
            }
        }

        $cacheDir  = RUNTIME_DIR . 'status_cache/';
        $cacheFile = $cacheDir . md5($imageUrl) . '.svg';
        if (!is_file($cacheFile)) {
            $image = file_get_contents($imageUrl);
            file_put_contents($cacheFile, $image);
        }

        $image = file_get_contents($cacheFile);

        $response = new Response();

        $response->setHeader('Content-Type', 'image/svg+xml');
        $response->setContent($image);

        return $response;
    }

    /**
     * View the public status page of a given project, if enabled.
     *
     * @param integer $projectId
     *
     * @return string
     *
     * @throws NotFoundException
     */
    public function view($projectId)
    {
        $project = $this->projectStore->getById($projectId);

        if (empty($project) || !$project->getAllowPublicStatus()) {
            throw new NotFoundException('Project with id: ' . $projectId . ' not found');
        }

        $builds = $this->getLatestBuilds($projectId);

        if (count($builds)) {
            $this->view->latest = $builds[0];
        }

        $this->view->builds  = $builds;
        $this->view->project = $project;

        return $this->view->render();
    }

    /**
     * Render latest builds for project as HTML table.
     *
     * @param integer $projectId
     *
     * @return array
     */
    protected function getLatestBuilds($projectId)
    {
        $criteria = ['project_id' => $projectId];
        $order    = ['id' => 'DESC'];
        $builds   = $this->buildStore->getWhere($criteria, 10, 0, $order);

        foreach ($builds['items'] as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        return $builds['items'];
    }
}
