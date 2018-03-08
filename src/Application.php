<?php

namespace PHPCensor;

use PHPCensor\Exception\HttpException;
use PHPCensor\Http\Response;
use PHPCensor\Http\Response\RedirectResponse;
use PHPCensor\Store\Factory;
use PHPCensor\Exception\HttpException\NotFoundException;
use PHPCensor\Http\Request;
use PHPCensor\Http\Router;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Application
{
    /**
     * @var array
     */
    protected $route;

    /**
     * @var Controller
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Config $config
     *
     * @param Request|null $request
     */
    public function __construct(Config $config, Request $request = null)
    {
        $this->config = $config;
        $this->response = new Response();

        if (!is_null($request)) {
            $this->request = $request;
        } else {
            $this->request = new Request();
        }

        $this->router = new Router($this, $this->request, $this->config);

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

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
                $user = Factory::getStore('User')->getByPrimaryKey($_SESSION['php-censor-user-id']);

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
     * @return Response
     *
     * @throws NotFoundException
     */
    protected function handleRequestInner()
    {
        $this->route = $this->router->dispatch();

        if (!empty($this->route['callback'])) {
            $callback = $this->route['callback'];

            if (!$callback($this->route, $this->response)) {
                return $this->response;
            }
        }

        if (!$this->controllerExists($this->route)) {
            throw new NotFoundException('Controller ' . $this->toPhpName($this->route['controller']) . ' does not exist!');
        }

        $action = lcfirst($this->toPhpName($this->route['action']));
        if (!$this->getController()->hasAction($action)) {
            throw new NotFoundException('Controller ' . $this->toPhpName($this->route['controller']) . ' does not have action ' . $action . '!');
        }

        return $this->getController()->handleAction($action, $this->route['args']);
    }

    /**
     * Handle an incoming web request.
     *
     * @return Response
     */
    public function handleRequest()
    {
        try {
            $this->response = $this->handleRequestInner();
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
     * @return Controller
     */
    protected function loadController($class)
    {
        /** @var Controller $controller */
        $controller = new $class($this->config, $this->request, $this->response);
        $controller->init();

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
        $groupStore = Factory::getStore('ProjectGroup');
        $groupList = $groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $thisGroup             = ['title' => $group->getTitle()];
            $projects              = Factory::getStore('Project')->getByGroupId($group->getId(), false);
            $thisGroup['projects'] = $projects['items'];
            $groups[]              = $thisGroup;
        }

        $archivedProjects          = Factory::getStore('Project')->getAll(true);
        $layout->archived_projects = $archivedProjects['items'];
        $layout->groups            = $groups;
    }

    /**
     * Check whether we should skip auth (because it is disabled)
     *
     * @return boolean
     */
    protected function shouldSkipAuth()
    {
        $config        = Config::getInstance();
        $disableAuth   = (bool)$config->get('php-censor.security.disable_auth', false);
        $defaultUserId = (integer)$config->get('php-censor.security.default_user_id', 1);

        if ($disableAuth && $defaultUserId) {
            $user = Factory::getStore('User')->getByPrimaryKey($defaultUserId);

            if ($user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        if (empty($this->controller)) {
            $controllerClass  = $this->getControllerClass($this->route);
            $this->controller = $this->loadController($controllerClass);
        }

        return $this->controller;
    }

    /**
     * @param array $route
     *
     * @return bool
     */
    protected function controllerExists($route)
    {
        return class_exists($this->getControllerClass($route));
    }

    /**
     * @param array $route
     *
     * @return string
     */
    protected function getControllerClass($route)
    {
        $namespace  = $this->toPhpName($route['namespace']);
        $controller = $this->toPhpName($route['controller']);

        return 'PHPCensor\\' . $namespace . '\\' . $controller . 'Controller';
    }

    /**
     * @param array $route
     *
     * @return boolean
     */
    public function isValidRoute(array $route)
    {
        if ($this->controllerExists($route)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function toPhpName($string)
    {
        $string = str_replace('-', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return $string;
    }
}
