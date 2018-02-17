<?php

namespace PHPCensor\Controller;

use b8;
use b8\Exception\HttpException\NotFoundException;
use b8\Form;
use b8\Store;
use JasonGrimes\Paginator;
use PHPCensor;
use PHPCensor\BuildFactory;
use PHPCensor\Helper\Github;
use PHPCensor\Helper\Lang;
use PHPCensor\Helper\SshKey;
use PHPCensor\Service\BuildService;
use PHPCensor\Service\ProjectService;
use PHPCensor\Model\Build;
use b8\Http\Response\RedirectResponse;
use PHPCensor\View;

/**
 * Project Controller - Allows users to create, edit and view projects.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectController extends PHPCensor\Controller
{
    /**
     * @var \PHPCensor\Store\ProjectStore
     */
    protected $projectStore;

    /**
     * @var \PHPCensor\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $buildStore;

    /**
     * @var \PHPCensor\Service\BuildService
     */
    protected $buildService;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->buildStore     = Store\Factory::getStore('Build');
        $this->projectStore   = Store\Factory::getStore('Project');
        $this->projectService = new ProjectService($this->projectStore);
        $this->buildService   = new BuildService($this->buildStore);
    }

    /**
     * @param int $projectId
     *
     * @return b8\Http\Response
     */
    public function ajaxBuilds($projectId)
    {
        $branch       = $this->getParam('branch', '');
        $environment  = $this->getParam('environment', '');
        $page         = (integer)$this->getParam('page', 1);
        $perPage      = (integer)$this->getParam('per_page', 10);
        $builds       = $this->getLatestBuildsHtml($projectId, $branch, $environment, (($page - 1) * $perPage), $perPage);

        $this->response->disableLayout();
        $this->response->setContent($builds[0]);

        return $this->response;
    }

    /**
     * View a specific project.
     *
     * @param integer $projectId
     *
     * @throws NotFoundException
     *
     * @return string
     */
    public function view($projectId)
    {
        $branch      = $this->getParam('branch', '');
        $environment = $this->getParam('environment', '');
        $page        = (integer)$this->getParam('page', 1);
        $project     = $this->projectStore->getById($projectId);

        if (empty($project)) {
            throw new NotFoundException(Lang::get('project_x_not_found', $projectId));
        }

        /** @var PHPCensor\Model\User $user */
        $user     = $this->getUser();
        $perPage  = $user->getFinalPerPage();
        $builds   = $this->getLatestBuildsHtml($projectId, $branch, $environment, (($page - 1) * $perPage), $perPage);
        $pages    = ($builds[1] === 0)
            ? 1
            : (integer)ceil($builds[1] / $perPage);

        if ($page > $pages) {
            $page = $pages;
        }

        $this->view->builds       = $builds[0];
        $this->view->total        = $builds[1];
        $this->view->project      = $project;
        $this->view->branch       = urldecode($branch);
        $this->view->branches     = $this->projectStore->getKnownBranches($projectId);
        $this->view->environment  = urldecode($environment);
        $this->view->environments = $project->getEnvironmentsNames();
        $this->view->page         = $page;
        $this->view->perPage      = $perPage;
        $this->view->paginator    = $this->getPaginatorHtml($projectId, $branch, $environment, $builds[1], $perPage, $page);

        $this->layout->title    = $project->getTitle();
        $this->layout->subtitle = '';

        if (!empty($this->view->environment)) {
            $this->layout->subtitle = '<i class="fa fa-gear"></i> ' . $this->view->environment;
        } elseif (!empty($this->view->branch)) {
            $this->layout->subtitle = '<i class="fa fa-code-fork"></i> ' . $this->view->branch;
        }

        return $this->view->render();
    }

    /**
     * @param integer $projectId
     * @param string  $branch
     * @param string  $environment
     * @param integer $total
     * @param integer $perPage
     * @param integer $page
     *
     * @return string
     */
    protected function getPaginatorHtml($projectId, $branch, $environment, $total, $perPage, $page)
    {
        $view = new View('pagination');

        $urlPattern = APP_URL . 'project/view/' . $projectId;
        $params     = [];
        if (!empty($branch)) {
            $params['branch'] = $branch;
        }

        if (!empty($environment)) {
            $params['environment'] = $environment;
        }

        $urlPattern = $urlPattern . '?' . str_replace('%28%3Anum%29', '(:num)', http_build_query(array_merge($params, ['page' => '(:num)'])));
        $paginator  = new Paginator($total, $perPage, $page, $urlPattern);

        $view->paginator = $paginator;

        return $view->render();
    }

    /**
     * Create a new pending build for a project.
     *
     * @param integer $projectId
     *
     * @throws NotFoundException
     *
     * @return RedirectResponse
     *
     */
    public function build($projectId)
    {
        /* @var \PHPCensor\Model\Project $project */
        $project = $this->projectStore->getById($projectId);
        if (empty($project) || $project->getArchived()) {
            throw new NotFoundException(Lang::get('project_x_not_found', $projectId));
        }

        $type  = $this->getParam('type', 'branch');
        $id    = $this->getParam('id');
        $debug = (boolean)$this->getParam('debug', false);

        $environment = null;
        $branch      = null;

        switch($type) {
            case 'environment':
                $environment = $id;
                break;
            case 'branch':
                $branch = $id;
                break;
        }

        if (empty($branch)) {
            $branch = $project->getBranch();
        }

        $extra = null;

        if ($debug && $this->currentUserIsAdmin()) {
            $extra = [
                'debug' => true,
            ];
        }

        /** @var PHPCensor\Model\User $user */
        $user  = $this->getUser();
        $build = $this->buildService->createBuild(
            $project,
            $environment,
            '',
            $branch,
            null,
            $user->getEmail(),
            null,
            Build::SOURCE_MANUAL_WEB,
            $user->getId(),
            $extra
        );

        if ($this->buildService->queueError) {
            $_SESSION['global_error'] = Lang::get('add_to_queue_failed');
        }

        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL.'build/view/' . $build->getId());

        return $response;
    }

    /**
    * Delete a project.
    */
    public function delete($projectId)
    {
        $this->requireAdmin();

        $project = $this->projectStore->getById($projectId);
        $this->projectService->deleteProject($project);

        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL);

        return $response;
    }

    /**
     * Render latest builds for project as HTML table.
     *
     * @param int    $projectId
     * @param string $branch      A urldecoded branch name.
     * @param string $environment A urldecoded environment name.
     * @param int    $start
     * @param int    $perPage
     *
     * @return array
     */
    protected function getLatestBuildsHtml($projectId, $branch = '', $environment = '', $start = 0, $perPage = 10)
    {
        $criteria = ['project_id' => $projectId];

        if (!empty($environment)) {
            $criteria['environment'] = $environment;
        }

        if (!empty($branch)) {
            $criteria['branch'] = $branch;
        }

        $order  = ['id' => 'DESC'];
        $builds = $this->buildStore->getWhere($criteria, $perPage, $start, [], $order);
        $view   = new View('Project/ajax-builds');

        foreach ($builds['items'] as &$build) {
            $build = BuildFactory::getBuild($build);
        }

        $view->builds = $builds['items'];

        return [
            $view->render(),
            (integer)$builds['count']
        ];
    }

    /**
    * Add a new project. Handles both the form, and processing.
    */
    public function add()
    {
        $this->layout->title = Lang::get('add_project');
        $this->requireAdmin();

        $method           = $this->request->getMethod();
        $pub              = null;
        $values           = $this->getParams();
        $values['branch'] = '';

        if ($method != 'POST') {
            $sshKey = new SshKey();
            $key    = $sshKey->generate();

            $values['key']    = $key['private_key'];
            $values['pubkey'] = $key['public_key'];
            $pub              = $key['public_key'];
        }

        $form = $this->projectForm($values);

        if ($method != 'POST' || ($method == 'POST' && !$form->validate())) {
            $view           = new View('Project/edit');
            $view->type     = 'add';
            $view->project  = null;
            $view->form     = $form;
            $view->key      = $pub;

            return $view->render();
        } else {
            $title     = $this->getParam('title', 'New Project');
            $reference = $this->getParam('reference', null);
            $type      = $this->getParam('type', null);

            $options = [
                'ssh_private_key'     => $this->getParam('key', null),
                'ssh_public_key'      => $this->getParam('pubkey', null),
                'build_config'        => $this->getParam('build_config', null),
                'allow_public_status' => $this->getParam('allow_public_status', 0),
                'branch'              => $this->getParam('branch', null),
                'default_branch_only' => $this->getParam('default_branch_only', 0),
                'group'               => $this->getParam('group_id', null),
                'environments'        => $this->getParam('environments', null),
            ];

            /** @var PHPCensor\Model\User $user */
            $user    = $this->getUser();
            $project = $this->projectService->createProject($title, $type, $reference, $user->getId(), $options);

            $response = new RedirectResponse();
            $response->setHeader('Location', APP_URL.'project/view/' . $project->getId());

            return $response;
        }
    }

    /**
    * Edit a project. Handles both the form and processing.
    */
    public function edit($projectId)
    {
        $this->requireAdmin();

        $method  = $this->request->getMethod();
        $project = $this->projectStore->getById($projectId);

        if (empty($project)) {
            throw new NotFoundException(Lang::get('project_x_not_found', $projectId));
        }

        $this->layout->title    = $project->getTitle();
        $this->layout->subtitle = Lang::get('edit_project');

        $values                 = $project->getDataArray();
        $values['key']          = $values['ssh_private_key'];
        $values['pubkey']       = $values['ssh_public_key'];
        $values['environments'] = $project->getEnvironments();

        if ($values['type'] == 'gitlab') {
            $accessInfo          = $project->getAccessInformation();
            $reference           = $accessInfo["user"] . '@' . $accessInfo["domain"] . ':' . $accessInfo["port"] . '/' . ltrim($project->getReference(), '/') . ".git";
            $values['reference'] = $reference;
        }

        if ($method == 'POST') {
            $values = $this->getParams();
        }

        $form = $this->projectForm($values, 'edit/' . $projectId);

        if ($method != 'POST' || ($method == 'POST' && !$form->validate())) {
            $view           = new View('Project/edit');
            $view->type     = 'edit';
            $view->project  = $project;
            $view->form     = $form;
            $view->key      = $values['pubkey'];

            return $view->render();
        }

        $title     = $this->getParam('title', Lang::get('new_project'));
        $reference = $this->getParam('reference', null);
        $type      = $this->getParam('type', null);

        $options = [
            'ssh_private_key'     => $this->getParam('key', null),
            'ssh_public_key'      => $this->getParam('pubkey', null),
            'build_config'        => $this->getParam('build_config', null),
            'allow_public_status' => $this->getParam('allow_public_status', 0),
            'archived'            => $this->getParam('archived', 0),
            'branch'              => $this->getParam('branch', null),
            'default_branch_only' => $this->getParam('default_branch_only', 0),
            'group'               => $this->getParam('group_id', null),
            'environments'        => $this->getParam('environments', null),
        ];

        $project = $this->projectService->updateProject($project, $title, $type, $reference, $options);

        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL.'project/view/' . $project->getId());

        return $response;
    }

    /**
    * Create add / edit project form.
    */
    protected function projectForm($values, $type = 'add')
    {
        $form = new Form();

        $form->setMethod('POST');
        $form->setAction(APP_URL.'project/' . $type);

        $form->addField(new Form\Element\Csrf('csrf'));
        $form->addField(new Form\Element\Hidden('pubkey'));

        $options = [
            'choose'      => Lang::get('select_repository_type'),
            'github'      => 'GitHub',
            'bitbucket'   => 'Bitbucket (Git)',
            'bitbuckethg' => 'Bitbucket (Hg)',
            'gitlab'      => 'GitLab',
            'gogs'        => 'Gogs',
            'remote'      => 'Git',
            'local'       => Lang::get('local'),
            'hg'          => 'Mercurial (Hg)',
            'svn'         => 'SVN',
        ];

        $field = Form\Element\Select::create('type', Lang::get('where_hosted'), true);
        $field->setPattern('^(github|bitbucket|bitbuckethg|gitlab|gogs|remote|local|hg|svn)');
        $field->setOptions($options);
        $field->setClass('form-control')->setContainerClass('form-group');
        $form->addField($field);

        $container = new Form\ControlGroup('github-container');
        $container->setClass('github-container');

        $field = Form\Element\Select::create('github', Lang::get('choose_github'), false);
        $field->setClass('form-control')->setContainerClass('form-group');
        $container->addField($field);
        $form->addField($container);

        $field = Form\Element\Text::create('reference', Lang::get('repo_name'), true);
        $field->setValidator($this->getReferenceValidator($values));
        $field->setClass('form-control')->setContainerClass('form-group');
        $form->addField($field);

        $field = Form\Element\Text::create('title', Lang::get('project_title'), true);
        $field->setClass('form-control')->setContainerClass('form-group');
        $form->addField($field);

        $field = Form\Element\Text::create('branch', Lang::get('default_branch'), false);
        $field->setClass('form-control')->setContainerClass('form-group')->setValue('');
        $form->addField($field);

        $field = Form\Element\Checkbox::create(
            'default_branch_only',
            Lang::get('default_branch_only'),
            false
        );
        $field->setContainerClass('form-group');
        $field->setCheckedValue(1);
        $field->setValue(0);
        $form->addField($field);

        $field = Form\Element\TextArea::create('key', Lang::get('project_private_key'), false);
        $field->setClass('form-control')->setContainerClass('form-group');
        $field->setRows(6);
        $form->addField($field);

        $field = Form\Element\TextArea::create('build_config', Lang::get('build_config'), false);
        $field->setClass('form-control')->setContainerClass('form-group');
        $field->setRows(6);
        $form->addField($field);

        $field = Form\Element\TextArea::create('environments', Lang::get('environments_label'), false);
        $field->setClass('form-control')->setContainerClass('form-group');
        $field->setRows(6);
        $form->addField($field);

        $field = Form\Element\Select::create('group_id', Lang::get('project_group'), true);
        $field->setClass('form-control')->setContainerClass('form-group')->setValue(1);

        $groups = [];
        $groupStore = b8\Store\Factory::getStore('ProjectGroup');
        $groupList = $groupStore->getWhere([], 100, 0, [], ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $groups[$group->getId()] = $group->getTitle();
        }

        $field->setOptions($groups);
        $form->addField($field);

        $field = Form\Element\Checkbox::create('allow_public_status', Lang::get('allow_public_status'), false);
        $field->setContainerClass('form-group');
        $field->setCheckedValue(1);
        $field->setValue(0);
        $form->addField($field);

        $field = Form\Element\Checkbox::create('archived', Lang::get('archived'), false);
        $field->setContainerClass('form-group');
        $field->setCheckedValue(1);
        $field->setValue(0);
        $form->addField($field);

        $field = new Form\Element\Submit();
        $field->setValue(Lang::get('save_project'));
        $field->setContainerClass('form-group');
        $field->setClass('btn-success');
        $form->addField($field);

        $form->setValues($values);

        return $form;
    }

    /**
     * Get the validator to use to check project references.
     * @param $values
     * @return callable
     */
    protected function getReferenceValidator($values)
    {
        return function ($val) use ($values) {
            $type = $values['type'];

            $validators = [
                'hg' => [
                    'regex'   => '/^(ssh|https?):\/\//',
                    'message' => Lang::get('error_mercurial')
                ],
                'remote' => [
                    'regex'   => '/^(git|https?):\/\//',
                    'message' => Lang::get('error_remote')
                ],
                'gitlab' => [
                    'regex'   => '`^(.*)@(.*):(.*)/(.*)\.git`',
                    'message' => Lang::get('error_gitlab')
                ],
                'github' => [
                    'regex'   => '/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/',
                    'message' => Lang::get('error_github')
                ],
                'bitbucket' => [
                    'regex'   => '/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/',
                    'message' => Lang::get('error_bitbucket')
                ],
                'bitbuckethg' => [
                    'regex'   => '/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/',
                    'message' => Lang::get('error_bitbucket')
                ],
            ];

            if (in_array($type, $validators) && !preg_match($validators[$type]['regex'], $val)) {
                throw new \Exception($validators[$type]['message']);
            } elseif ($type == 'local' && !is_dir($val)) {
                throw new \Exception(Lang::get('error_path'));
            }

            return true;
        };
    }

    /**
     * Get an array of repositories from Github's API.
     */
    public function ajaxGithubRepositories()
    {
        $github = new Github();

        $response = new b8\Http\Response\JsonResponse();
        $response->setContent($github->getRepositories());

        return $response;
    }
}
