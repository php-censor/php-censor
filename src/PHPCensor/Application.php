<?php

namespace PHPCensor;

use b8;
use b8\Exception\HttpException;
use b8\Http\Response;
use b8\Http\Response\RedirectResponse;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Application extends b8\Application
{
    /**
     * @var \PHPCensor\Controller
     */
    protected $controller;

    /**
     * Initialise Application - Handles session verification, routing, etc.
     */
    public function init()
    {
        $request =& $this->request;
        $route   = '/:controller/:action';
        $opts    = ['controller' => 'Home', 'action' => 'index'];

        // Inlined as a closure to fix "using $this when not in object context" on 5.3
        $validateSession = function () {
            if (!empty($_SESSION['php-censor-user-id'])) {
                $user = b8\Store\Factory::getStore('User')->getByPrimaryKey($_SESSION['php-censor-user-id']);

                if ($user) {
                    return true;
                }
            }

            return false;
        };

        $skipAuth = [$this, 'shouldSkipAuth'];

        // Handler for the route we're about to register, checks for a valid session where necessary:
        $routeHandler = function (&$route, Response &$response) use (&$request, $validateSession, $skipAuth) {
            $skipValidation = in_array($route['controller'], ['session', 'webhook', 'build-status']);

            if (!$skipValidation && !$validateSession() && (!is_callable($skipAuth) || !$skipAuth())) {
                if ($request->isAjax()) {
                    $response->setResponseCode(401);
                    $response->setContent('');
                } else {
                    $_SESSION['php-censor-login-redirect'] = substr($request->getPath(), 1);
                    $response = new RedirectResponse($response);
                    $response->setHeader('Location', APP_URL . 'session/login');
                }

                return false;
            }

            return true;
        };

        $this->router->clearRoutes();
        $this->router->register($route, $opts, $routeHandler);
    }

    /**
     * Handle an incoming web request.
     *
     * @return Response
     */
    public function handleRequest()
    {
        try {
            $this->response = parent::handleRequest();
        } catch (HttpException $ex) {
            $this->config->set('page_title', 'Error');

            $view = new View('exception');
            $view->exception = $ex;

            $this->response->setResponseCode($ex->getErrorCode());
            $this->response->setContent($view->render());
        } catch (\Exception $ex) {
            $this->config->set('page_title', 'Error');

            $view = new View('exception');
            $view->exception = $ex;

            $this->response->setResponseCode(500);
            $this->response->setContent($view->render());
        }

        if ($this->response->hasLayout() && $this->controller && $this->controller->layout) {
            $this->setLayoutVariables($this->controller->layout);

            $this->controller->layout->content  = $this->response->getContent();
            $this->response->setContent($this->controller->layout->render());
        }

        return $this->response;
    }

    /**
     * Loads a particular controller, and injects our layout view into it.
     *
     * @param string $class
     *
     * @return b8\Controller
     */
    protected function loadController($class)
    {
        $controller                     = parent::loadController($class);
        $controller->layout             = new View('layout');
        $controller->layout->title      = 'PHP Censor';
        $controller->layout->breadcrumb = [];
        $controller->layout->version    = trim(file_get_contents(ROOT_DIR . 'VERSION.md'));

        return $controller;
    }

    /**
     * Injects variables into the layout before rendering it.
     *
     * @param View $layout
     */
    protected function setLayoutVariables(View &$layout)
    {
        $groups = [];
        $groupStore = b8\Store\Factory::getStore('ProjectGroup');
        $groupList = $groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $thisGroup             = ['title' => $group->getTitle()];
            $projects              = b8\Store\Factory::getStore('Project')->getByGroupId($group->getId(), false);
            $thisGroup['projects'] = $projects['items'];
            $groups[]              = $thisGroup;
        }

        $archived_projects         = b8\Store\Factory::getStore('Project')->getAll(true);
        $layout->archived_projects = $archived_projects['items'];
        $layout->groups            = $groups;
    }

    /**
     * Check whether we should skip auth (because it is disabled)
     *
     * @return boolean
     */
    protected function shouldSkipAuth()
    {
        $config        = b8\Config::getInstance();
        $disableAuth   = (bool)$config->get('php-censor.security.disable_auth', false);
        $defaultUserId = (integer)$config->get('php-censor.security.default_user_id', 1);

        if ($disableAuth && $defaultUserId) {
            $user = b8\Store\Factory::getStore('User')->getByPrimaryKey($defaultUserId);

            if ($user) {
                return true;
            }
        }

        return false;
    }
}
