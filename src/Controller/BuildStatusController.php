<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use Exception;
use PHPCensor\BuildFactory;
use PHPCensor\Exception\HttpException;
use PHPCensor\Exception\HttpException\NotFoundException;
use PHPCensor\Common\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildStatusService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\WebController;
use SimpleXMLElement;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildStatusController extends WebController
{
    public string $layoutName = 'layoutPublic';

    protected ProjectStore $projectStore;

    protected BuildStore $buildStore;

    protected BuildFactory $buildFactory;

    public function init(): void
    {
        parent::init();

        $this->buildStore   = $this->storeRegistry->get('Build');
        $this->projectStore = $this->storeRegistry->get('Project');

        $this->buildFactory = new BuildFactory(
            $this->configuration,
            $this->storeRegistry
        );
    }

    /**
     * Returns status of the last build
     */
    protected function getStatus(Project $project, string $branch): string
    {
        $status = 'passing';
        try {
            $build = $project->getLatestBuild($branch, [
                Build::STATUS_SUCCESS,
                Build::STATUS_FAILED,
            ]);

            if (isset($build) && $build instanceof Build && $build->getStatus() !== Build::STATUS_SUCCESS) {
                $status = 'failed';
            }
        } catch (Exception $e) {
            $status = 'error';
        }

        return $status;
    }

    /**
     * Returns coverage of the last build
     *
     * @return string
     */
    protected function getPhpunitCoverage(Project $project, string $branch, string $type = 'lines')
    {
        $coverage = 0;
        if (!\in_array($type, ['classes', 'methods', 'lines'], true)) {
            $type = 'lines';
        }

        try {
            $build = $project->getLatestBuild($branch, [
                Build::STATUS_SUCCESS,
                Build::STATUS_FAILED,
            ]);

            if (isset($build) && $build instanceof Build) {
                $coverageMeta = $this->buildStore->getMeta(
                    'php_unit-coverage',
                    $build->getProjectId(),
                    $build->getId(),
                    $build->getBranch()
                );

                if ($coverageMeta && isset($coverageMeta[0]['meta_value'][$type])) {
                    $coverage = $coverageMeta[0]['meta_value'][$type];
                }
            }
        } catch (Exception $e) {
        }

        return $coverage;
    }

    protected function renderXml(?SimpleXMLElement $xml = null): Response
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($xml->asXML());

        return $response;
    }

    /**
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    protected function getLatestBuilds(int $projectId, string $branch): array
    {
        $criteria = [
            'project_id' => $projectId,
            'branch'     => $branch,
        ];

        $order  = ['id' => 'DESC'];
        $builds = $this->buildStore->getWhere($criteria, 10, 0, $order);

        foreach ($builds['items'] as &$build) {
            $build = $this->buildFactory->getBuild($build);
        }

        return $builds['items'];
    }

    /**
     * Returns the appropriate build PHPUnit coverage image in SVG format for a given project.
     *
     * @throws HttpException
     */
    public function phpunitCoverageImage(int $projectId): Response
    {
        /** @var Project $project */
        $project = $this->projectStore->getById($projectId);

        // plastic|flat|flat-squared|social
        $style  = $this->getParam('style', 'flat');
        $label  = $this->getParam('label', 'build');
        $type   = $this->getParam('type', 'lines');
        $branch = $this->getParam('branch', $project->getDefaultBranch());

        $optionalParams = [
            'logo'      => $this->getParam('logo'),
            'logoWidth' => $this->getParam('logoWidth'),
            'link'      => $this->getParam('link'),
            'maxAge'    => $this->getParam('maxAge'),
        ];

        $coverage = $this->getPhpunitCoverage($project, $branch, $type);
        $imageUrl = \sprintf(
            'http://img.shields.io/badge/%s-%s-%s.svg?style=%s',
            $label,
            $coverage. '%25',
            'green',
            $style
        );

        foreach ($optionalParams as $paramName => $param) {
            if ($param) {
                $imageUrl .= '&' . $paramName . '=' . $param;
            }
        }

        $cacheDir  = RUNTIME_DIR . 'status_cache/';
        $cacheFile = $cacheDir . \md5($imageUrl) . '.svg';
        if (!\is_file($cacheFile)) {
            $image = \file_get_contents($imageUrl);
            \file_put_contents($cacheFile, $image);
        }

        $image = \file_get_contents($cacheFile);

        $response = new Response();

        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->setContent($image);

        return $response;
    }

    /**
     * Returns the appropriate build status image in SVG format for a given project.
     *
     * @throws HttpException
     */
    public function image(int $projectId): Response
    {
        $project = $this->projectStore->getById($projectId);

        // plastic|flat|flat-squared|social
        $style  = $this->getParam('style', 'flat');
        $label  = $this->getParam('label', 'build');
        $branch = $this->getParam('branch', $project->getDefaultBranch());

        $optionalParams = [
            'logo'      => $this->getParam('logo'),
            'logoWidth' => $this->getParam('logoWidth'),
            'link'      => $this->getParam('link'),
            'maxAge'    => $this->getParam('maxAge'),
        ];

        $status = $this->getStatus($project, $branch);

        if (\is_null($status)) {
            return new RedirectResponse('/');
        }

        $color    = ($status === 'passing') ? 'green' : 'red';
        $imageUrl = \sprintf(
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
        $cacheFile = $cacheDir . \md5($imageUrl) . '.svg';
        if (!\is_file($cacheFile)) {
            $image = \file_get_contents($imageUrl);
            \file_put_contents($cacheFile, $image);
        }

        $image = \file_get_contents($cacheFile);

        $response = new Response();

        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->setContent($image);

        return $response;
    }

    /**
     * View the public status page of a given project, if enabled.
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     */
    public function view(int $projectId): string
    {
        $project = $this->projectStore->getById($projectId);
        $branch  = $this->getParam('branch', $project->getDefaultBranch());

        if (empty($project) || !$project->getAllowPublicStatus()) {
            throw new NotFoundException('Project with id: ' . $projectId . ' not found');
        }

        $builds = $this->getLatestBuilds($projectId, $branch);

        if (\count($builds)) {
            $this->view->latest = $builds[0];
        }

        $this->view->builds           = $builds;
        $this->view->project          = $project;
        $this->view->environmentStore = $this->storeRegistry->get('Environment');

        return $this->view->render();
    }

    /**
     * Displays projects information in ccmenu format
     *
     * @throws Exception
     */
    public function ccxml(int $projectId): Response
    {
        /* @var Project $project */
        $project = $this->projectStore->getById($projectId);
        $xml     = new SimpleXMLElement('<Projects/>');

        if (!$project instanceof Project || !$project->getAllowPublicStatus()) {
            return $this->renderXml($xml);
        }

        try {
            $branchList = $this->buildStore->getBuildBranches($projectId);

            if (!$branchList) {
                $branchList = [$project->getDefaultBranch()];
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
        } catch (Exception $e) {
            $xml = new SimpleXMLElement('<projects/>');
        }

        return $this->renderXml($xml);
    }
}
