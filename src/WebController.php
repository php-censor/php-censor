<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Exception\HttpException;
use PHPCensor\Exception\HttpException\ForbiddenException;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\SecretStore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;
use PHPCensor\Common\Application\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class WebController extends Controller
{
    protected string $className;

    protected ?View $view = null;

    public string $layoutName = '';

    public ?View $layout = null;

    protected UserStore $userStore;
    protected ProjectStore $projectStore;
    protected ProjectGroupStore $projectGroupStore;
    protected BuildStore $buildStore;
    protected BuildErrorStore $buildErrorStore;
    protected EnvironmentStore $environmentStore;
    protected SecretStore $secretStore;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        Request $request,
        Session $session,
        UserStore $userStore,
        ProjectStore $projectStore,
        ProjectGroupStore $projectGroupStore,
        BuildStore $buildStore,
        BuildErrorStore $buildErrorStore,
        EnvironmentStore $environmentStore,
        SecretStore $secretStore
    ) {
        parent::__construct($configuration, $storeRegistry, $request, $session);

        $this->userStore = $userStore;
        $this->projectStore = $projectStore;
        $this->projectGroupStore = $projectGroupStore;
        $this->buildStore = $buildStore;
        $this->buildErrorStore = $buildErrorStore;
        $this->environmentStore = $environmentStore;
        $this->secretStore = $secretStore;

        $class           = \explode('\\', \get_class($this));
        $this->className = \substr(\array_pop($class), 0, -10);
    }

    public function init(): void
    {
        if (!empty($this->layoutName)) {
            $this->layout = new View($this->layoutName);

            $this->layout->title      = 'PHP Censor';
            $this->layout->breadcrumb = [];

            $version = (string)\trim(\file_get_contents(ROOT_DIR . 'VERSION.md'));
            $version = !empty($version) ? $version : '0.0.0 (UNKNOWN)';

            $this->layout->version         = $version;
            $this->layout->isLoginDisabled = (bool)$this->configuration->get('php-censor.security.disable_auth', false);

            $this->layout->globalError = $this->session->get('global_error');
            $this->session->remove('global_error');

            $groups    = [];
            $groupList = $this->projectGroupStore->getWhere([], 100, 0, ['title' => 'ASC']);

            foreach ($groupList['items'] as $group) {
                $thisGroup             = ['title' => $group->getTitle()];
                $projects              = $this->projectStore->getByGroupId($group->getId(), false);
                $thisGroup['projects'] = $projects['items'];
                $groups[]              = $thisGroup;
            }

            $archivedProjects               = $this->projectStore->getAll('read', true);
            $this->layout->archivedProjects = $archivedProjects['items'];
            $this->layout->groups           = $groups;
            $this->layout->user             = $this->getUser();
        }
    }

    /**
     * Handle the incoming request.
     *
     * @throws Common\Exception\RuntimeException
     */
    public function handleAction(string $action, array $actionParams): Response
    {
        if (View::exists($this->className . '/' . $action)) {
            $this->view = new View($this->className . '/' . $action);
        }

        $result = parent::handleAction($action, $actionParams);

        if ($result instanceof Response) {
            return $result;
        }

        $content = '';
        if (\is_string($result)) {
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
    protected function requireAdmin(): void
    {
        if (!$this->currentUserIsAdmin()) {
            throw new ForbiddenException('You do not have permission to do that.');
        }
    }

    /**
     * Check if the currently logged in user is an administrator.
     *
     * @throws HttpException
     */
    protected function currentUserIsAdmin(): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        return $this->getUser()->getIsAdmin();
    }

    /**
     * @throws Common\Exception\RuntimeException
     * @throws HttpException
     */
    protected function getUser(): ?User
    {
        $sessionUserId = $this->session->get('php-censor-user-id');
        if (empty($sessionUserId)) {
            return null;
        }

        return $this->userStore->getById((int)$sessionUserId);
    }
}
