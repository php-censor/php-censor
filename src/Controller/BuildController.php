<?php

declare(strict_types = 1);

namespace PHPCensor\Controller;

use JasonGrimes\Paginator;
use PHPCensor\BuildFactory;
use PHPCensor\Exception\HttpException\NotFoundException;
use PHPCensor\Helper\Lang;
use PHPCensor\Http\Response;
use PHPCensor\Http\Response\JsonResponse;
use PHPCensor\Http\Response\RedirectResponse;
use PHPCensor\Model\Build;
use PHPCensor\Model\User;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\View;
use PHPCensor\WebController;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildController extends WebController
{
    public string $layoutName = 'layout';

    protected BuildStore $buildStore;

    protected ProjectStore $projectStore;

    protected BuildService $buildService;

    public function init(): void
    {
        parent::init();

        $this->buildStore   = $this->storeRegistry->get('Build');
        $this->projectStore = $this->storeRegistry->get('Project');

        $this->buildService = new BuildService(
            $this->configuration,
            $this->storeRegistry,
            $this->buildStore,
            $this->projectStore
        );
    }

    /**
     * View a specific build.
     *
     * @param int $buildId
     *
     * @throws NotFoundException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function view(int $buildId): void
    {
        $page   = (int)$this->getParam('page', 1);
        $plugin = $this->getParam('plugin', '');
        $isNew  = $this->getParam('is_new', '');

        $severity = $this->getParam('severity');
        if (null !== $severity && '' !== $severity) {
            $severity = (int)$severity;
        } else {
            $severity = null;
        }

        $build = BuildFactory::getBuildById(
            $this->configuration,
            $this->storeRegistry,
            (int)$buildId
        );

        if (!$build) {
            throw new NotFoundException(Lang::get('build_x_not_found', $buildId));
        }

        /** @var User $user */
        $user    = $this->getUser();
        $perPage = $user->getFinalPerPage($this->configuration);
        $data    = $this->getBuildData($build, $plugin, $severity, $isNew, (($page - 1) * $perPage), $perPage);
        $pages   = ($data['errors'] === 0)
            ? 1
            : (int)ceil($data['errors'] / $perPage);

        if ($page > $pages) {
            $page = $pages;
        }

        /** @var BuildErrorStore $errorStore */
        $errorStore = $this->storeRegistry->get('BuildError');

        $this->view->uiPlugins   = $this->getUiPlugins();
        $this->view->build       = $build;
        $this->view->data        = $data;
        $this->view->environment = $this->storeRegistry->get('Environment')->getById((int)$build->getEnvironmentId());

        $this->view->plugin     = urldecode($plugin);
        $this->view->plugins    = $errorStore->getKnownPlugins($buildId, $severity, $isNew);
        $this->view->severity   = urldecode(null !== $severity ? $severity : '');
        $this->view->severities = $errorStore->getKnownSeverities($buildId, $plugin, $isNew);
        $this->view->isNew      = urldecode($isNew);
        $this->view->isNews     = ['only_new', 'only_old'];

        $this->view->page      = $page;
        $this->view->perPage   = $perPage;
        $this->view->paginator = $this->getPaginatorHtml(
            $buildId,
            $plugin,
            $severity,
            $isNew,
            $data['errors'],
            $perPage,
            $page
        );

        $this->layout->title = Lang::get('build_n', $buildId);
        $this->layout->subtitle = $build->getProjectTitle();

        switch ($build->getStatus()) {
            case 0:
                $this->layout->skin = 'blue';
                break;

            case 1:
                $this->layout->skin = 'yellow';
                break;

            case 2:
                $this->layout->skin = 'green';
                break;

            case 3:
                $this->layout->skin = 'red';
                break;
        }

        $rebuild     = Lang::get('rebuild_now');
        $rebuildLink = APP_URL . 'build/rebuild/' . $build->getId();

        $rebuildDebug = Lang::get('rebuild_now_debug');

        $delete     = Lang::get('delete_build');
        $deleteLink = APP_URL . 'build/delete/' . $build->getId();

        $project = $this->storeRegistry->get('Project')->getByPrimaryKey((int)$build->getProjectId());

        $actions = '';
        if (!$project->getArchived()) {
            if ($this->currentUserIsAdmin()) {
                $actions .= "<a class=\"btn btn-warning\" href=\"{$rebuildLink}?debug=1\">{$rebuildDebug}</a> ";
            }
            $actions .= "<a class=\"btn btn-success\" href=\"{$rebuildLink}\">{$rebuild}</a> ";
        }

        if ($this->currentUserIsAdmin()) {
            $actions .= " <a class=\"btn btn-danger\" id=\"delete-build\" href=\"{$deleteLink}\">{$delete}</a>";
        }

        $this->layout->actions = $actions;
    }

    /**
     * Returns an array of the JS plugins to include.
     *
     * @return array
     */
    protected function getUiPlugins(): array
    {
        $rtn  = [];
        $path = PUBLIC_DIR . 'assets/js/build-plugins/';
        $dir  = opendir($path);

        while ($item = readdir($dir)) {
            if (substr($item, 0, 1) == '.' || substr($item, -3) != '.js') {
                continue;
            }

            $rtn[] = $item;
        }

        return $rtn;
    }

    /**
     * Get build data from database and json encode it.
     *
     * @param Build $build
     * @param string $plugin
     * @param int $severity
     * @param string $isNew
     * @param int $start
     * @param int $perPage
     *
     * @return array
     *
     * @throws \PHPCensor\Common\Exception\InvalidArgumentException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    protected function getBuildData(
        Build $build,
        string $plugin,
        ?int $severity,
        string $isNew,
        int $start = 0,
        int $perPage = 10
    ): array {
        $data                = [];
        $data['status']      = (int)$build->getStatus();
        $data['log']         = $this->cleanLog((string)$build->getLog());

        $data['create_date'] = !\is_null($build->getCreateDate())
            ? $build->getCreateDate()->format('Y-m-d H:i:s')
            : null;

        $data['start_date'] = !\is_null($build->getStartDate())
            ? $build->getStartDate()->format('Y-m-d H:i:s')
            : null;

        $data['finish_date'] = !\is_null($build->getFinishDate())
            ? $build->getFinishDate()->format('Y-m-d H:i:s')
            : null;

        $data['duration'] = $build->getDuration();

        /** @var BuildErrorStore $errorStore */
        $errorStore = $this->storeRegistry->get('BuildError');
        $errors     = $errorStore->getByBuildId($build->getId(), $perPage, $start, $plugin, $severity, $isNew);

        $errorView         = new View('Build/errors');
        $errorView->build  = $build;
        $errorView->errors = $errors['items'];

        $data['errors']       = $build->getTotalErrorsCount($plugin, $severity, $isNew);
        $data['errors_total'] = (int)$build->getErrorsTotal();
        $data['error_html']   = $errorView->render();

        return $data;
    }

    /**
     * @param int $buildId
     * @param string  $plugin
     * @param int $severity
     * @param string  $isNew
     * @param int $total
     * @param int $perPage
     * @param int $page
     *
     * @return string
     */
    protected function getPaginatorHtml(
        int $buildId,
        string $plugin,
        ?int $severity,
        string $isNew,
        int $total,
        int $perPage,
        int $page
    ): string {
        $view = new View('pagination');

        $urlPattern = APP_URL . 'build/view/' . $buildId;
        $params     = [];
        if (!empty($plugin)) {
            $params['plugin'] = $plugin;
        }

        if (null !== $severity) {
            $params['severity'] = $severity;
        }

        if (!empty($isNew)) {
            $params['is_new'] = $isNew;
        }

        $urlPattern = $urlPattern . '?' . \str_replace(
            '%28%3Anum%29',
            '(:num)',
            \http_build_query(\array_merge($params, ['page' => '(:num)']))
        ) . '#errors';
        $paginator = new Paginator($total, $perPage, $page, $urlPattern);

        $view->paginator = $paginator;

        return $view->render();
    }

    /**
     * Create a build using an existing build as a template:
     *
     * @param int $buildId
     *
     * @return Response
     *
     * @throws NotFoundException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function rebuild(int $buildId): Response
    {
        $copy = BuildFactory::getBuildById(
            $this->configuration,
            $this->storeRegistry,
            (int)$buildId
        );

        $project = $this->storeRegistry->get('Project')->getByPrimaryKey((int)$copy->getProjectId());

        if (!$copy || $project->getArchived()) {
            throw new NotFoundException(Lang::get('build_x_not_found', $buildId));
        }

        $debug = (bool)$this->getParam('debug', false);

        if ($debug && $this->currentUserIsAdmin()) {
            $copy->addExtraValue('debug', true);
        } elseif (!$debug) {
            $copy->removeExtraValue('debug');
        }

        $build = $this->buildService->createDuplicateBuild($copy, Build::SOURCE_MANUAL_REBUILD_WEB);

        if ($this->buildService->queueError) {
            $_SESSION['global_error'] = Lang::get('add_to_queue_failed');
        }

        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL.'build/view/' . $build->getId());

        return $response;
    }

    /**
     * Delete a build.
     *
     * @param int $buildId
     *
     * @return Response
     *
     * @throws NotFoundException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function delete(int $buildId): Response
    {
        $this->requireAdmin();

        $build = BuildFactory::getBuildById(
            $this->configuration,
            $this->storeRegistry,
            (int)$buildId
        );

        if (!$build) {
            throw new NotFoundException(Lang::get('build_x_not_found', $buildId));
        }

        $this->buildService->deleteBuild($build);

        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL.'project/view/' . $build->getProjectId());

        return $response;
    }

    /**
     * Parse log for unix colours and replace with HTML.
     *
     * @param string $log
     *
     * @return string
     */
    protected function cleanLog(string $log): string
    {
        $converter = new AnsiToHtmlConverter(null, false);

        return $converter->convert($log);
    }

    /**
     * Formats a list of builds into rows suitable for the dropdowns in the header bar.
     *
     * @param array $builds
     *
     * @return array
     */
    protected function formatBuilds(array $builds): array
    {
        $rtn = ['count' => $builds['count'], 'items' => []];

        /** @var Build $build */
        foreach ($builds['items'] as $build) {
            $header        = new View('Build/header-row');
            $header->build = $build;

            $rtn['items'][$build->getId()]['header_row'] = $header->render();
        }

        \ksort($rtn['items']);
        return $rtn;
    }

    public function ajaxData(int $buildId): Response
    {
        $page    = (int)$this->getParam('page', 1);
        $perPage = (int)$this->getParam('per_page', 10);
        $plugin  = $this->getParam('plugin');
        $isNew   = $this->getParam('is_new');

        $severity = $this->getParam('severity');
        if (null !== $severity && '' !== $severity) {
            $severity = (int)$severity;
        } else {
            $severity = null;
        }

        $response = new JsonResponse();
        $build = BuildFactory::getBuildById(
            $this->configuration,
            $this->storeRegistry,
            (int)$buildId
        );

        if (!$build) {
            $response->setResponseCode(404);
            $response->setContent([]);

            return $response;
        }

        $data = $this->getBuildData(
            $build,
            $plugin,
            $severity,
            $isNew,
            (($page - 1) * $perPage),
            $perPage
        );

        $data['paginator'] = $this->getPaginatorHtml(
            $buildId,
            $plugin,
            $severity,
            $isNew,
            $data['errors'],
            $perPage,
            $page
        );

        $response->setContent($data);

        return $response;
    }

    public function ajaxMeta(int $buildId): Response
    {
        $build = BuildFactory::getBuildById(
            $this->configuration,
            $this->storeRegistry,
            (int)$buildId
        );

        $key       = $this->getParam('key');
        $numBuilds = (int)$this->getParam('num_builds', 1);
        $data      = null;

        if ($key && $build) {
            $data = $this->buildStore->getMeta($key, $build->getProjectId(), $buildId, $build->getBranch(), $numBuilds);
        }

        $response = new JsonResponse();
        $response->setContent($data);

        return $response;
    }

    public function ajaxQueue(): Response
    {
        $rtn = [
            'pending' => $this->formatBuilds($this->buildStore->getByStatus(Build::STATUS_PENDING)),
            'running' => $this->formatBuilds($this->buildStore->getByStatus(Build::STATUS_RUNNING)),
        ];

        $response = new JsonResponse();
        $response->setContent($rtn);

        return $response;
    }
}
