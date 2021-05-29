<?php

namespace PHPCensor;

use PHPCensor\Exception\HttpException;
use PHPCensor\Exception\HttpException\ForbiddenException;
use PHPCensor\Http\Request;
use PHPCensor\Http\Response;
use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;

abstract class WebController extends Controller
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var View
     */
    protected $view = null;

    /**
     * @var string
     */
    public $layoutName = '';

    /**
     * @var View
     */
    public $layout = null;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        Request $request
    ) {
        parent::__construct($configuration, $storeRegistry, $request);

        $class           = explode('\\', get_class($this));
        $this->className = substr(array_pop($class), 0, -10);
    }

    public function init()
    {
        if (!empty($this->layoutName)) {
            $this->layout = new View($this->layoutName);

            $this->layout->title      = 'PHP Censor';
            $this->layout->breadcrumb = [];

            $version = (string)\trim(\file_get_contents(ROOT_DIR . 'VERSION.md'));
            $version = !empty($version) ? $version : '0.0.0 (UNKNOWN)';

            $this->layout->version         = $version;
            $this->layout->isLoginDisabled = (bool)$this->configuration->get('php-censor.security.disable_auth', false);

            $groups     = [];
            $groupStore = $this->storeRegistry->get('ProjectGroup');
            $groupList  = $groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

            foreach ($groupList['items'] as $group) {
                $thisGroup             = ['title' => $group->getTitle()];
                $projects              = $this->storeRegistry->get('Project')->getByGroupId($group->getId(), false);
                $thisGroup['projects'] = $projects['items'];
                $groups[]              = $thisGroup;
            }

            $archivedProjects               = $this->storeRegistry->get('Project')->getAll(true);
            $this->layout->archivedProjects = $archivedProjects['items'];
            $this->layout->groups           = $groups;
            $this->layout->user             = $this->getUser();
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param string $action
     * @param array  $actionParams
     *
     * @return Response
     */
    public function handleAction($action, $actionParams)
    {
        if (View::exists($this->className . '/' . $action)) {
            $this->view = new View($this->className . '/' . $action);
        }

        $result = parent::handleAction($action, $actionParams);

        if ($result instanceof Response) {
            return $result;
        }

        $content = '';
        if (is_string($result)) {
            $content = $result;
        } elseif ($this->view) {
            $content = $this->view->render();
        }

        $response = new Response();
        if ($this->layout) {
            $this->layout->content = $content;

            $response->setContent($this->layout->render());
        } else {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Require that the currently logged in user is an administrator.
     *
     * @throws HttpException
     */
    protected function requireAdmin()
    {
        if (!$this->currentUserIsAdmin()) {
            throw new ForbiddenException('You do not have permission to do that.');
        }
    }

    /**
     * Check if the currently logged in user is an administrator.
     *
     * @return bool
     *
     * @return bool
     *
     * @throws HttpException
     */
    protected function currentUserIsAdmin()
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        return $this->getUser()->getIsAdmin();
    }

    /**
     * @return User|null
     *
     * @throws HttpException
     */
    protected function getUser()
    {
        if (empty($_SESSION['php-censor-user-id'])) {
            return null;
        }

        /** @var UserStore $userStore */
        $userStore = $this->storeRegistry->get('User');

        return $userStore->getById($_SESSION['php-censor-user-id']);
    }
}
