<?php

namespace PHPCensor\Controller;

use b8;
use b8\Exception\HttpException\NotFoundException;
use b8\Store;
use PHPCensor\BuildFactory;
use PHPCensor\Model\Project;
use PHPCensor\Model\Build;
use PHPCensor\Service\BuildStatusService;
use PHPCensor\Controller;

/**
 * Build Status Controller - Allows external access to build status information / images.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildStatusController extends Controller
{
    /* @var \PHPCensor\Store\ProjectStore */
    protected $projectStore;

    /* @var \PHPCensor\Store\BuildStore */
    protected $buildStore;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->response->disableLayout();

        $this->buildStore   = Store\Factory::getStore('Build');
        $this->projectStore = Store\Factory::getStore('Project');
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
     * @return bool
     *
     * @throws \Exception
     * @throws b8\Exception\HttpException
     */
    public function ccxml($projectId)
    {
        /* @var Project $project */
        $project = $this->projectStore->getById($projectId);
        $xml = new \SimpleXMLElement('<Projects/>');

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
     * @return boolean
     */
    protected function renderXml(\SimpleXMLElement $xml = null)
    {
        $this->response->setHeader('Content-Type', 'text/xml');
        $this->response->setContent($xml->asXML());
        $this->response->flush();
        echo $xml->asXML();

        return true;
    }

    /**
     * Returns the appropriate build status image in SVG format for a given project.
     *
     * @param $projectId
     *
     * @return b8\Http\Response|b8\Http\Response\RedirectResponse
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
            $response = new b8\Http\Response\RedirectResponse();
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

        $this->response->disableLayout();
        $this->response->setHeader('Content-Type', 'image/svg+xml');
        $this->response->setContent($image);
        return $this->response;
    }

    /**
     * View the public status page of a given project, if enabled.
     *
     * @param integer $projectId
     *
     * @return string
     *
     * @throws \b8\Exception\HttpException\NotFoundException
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
