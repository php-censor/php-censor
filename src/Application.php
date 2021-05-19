<?php

namespace PHPCensor;

use Exception;
use PHPCensor\Exception\HttpException;
use PHPCensor\Exception\HttpException\NotFoundException;
use PHPCensor\Http\Request;
use PHPCensor\Http\Response;
use PHPCensor\Http\Response\RedirectResponse;
use PHPCensor\Http\Router;
use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Application
{
    private ?array $route;

    private Controller $controller;

    private Request $request;

    private ConfigurationInterface $configuration;

    private DatabaseManager $databaseManager;

    private StoreRegistry $storeRegistry;

    private Router $router;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        ?Request $request = null
    ) {
        $this->configuration   = $configuration;
        $this->databaseManager = $databaseManager;
        $this->storeRegistry   = $storeRegistry;

        $this->request = new Request();
        if (!is_null($request)) {
            $this->request = $request;
        }

        $this->router = new Router($this, $this->request);

        $this->init();
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
                $user = $this->storeRegistry->get('User')->getByPrimaryKey($_SESSION['php-censor-user-id']);

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

            $response = new Response();
            if (!$callback($this->route, $response)) {
                return $response;
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
     * @return User|null
     *
     * @throws HttpException
     */
    private function getUser()
    {
        if (empty($_SESSION['php-censor-user-id'])) {
            return null;
        }

        /** @var UserStore $userStore */
        $userStore = $this->storeRegistry->get('User');

        return $userStore->getById($_SESSION['php-censor-user-id']);
    }

    /**
     * Handle an incoming web request.
     *
     * @return Response
     */
    public function handleRequest()
    {
        try {
            $response = $this->handleRequestInner();
        } catch (HttpException $ex) {
            $view = new View('exception');
            $view->exception = $ex;
            $view->user      = $this->getUser();

            $response = new Response();

            $response->setResponseCode($ex->getErrorCode());
            $response->setContent($view->render());
        } catch (Exception $ex) {
            $view = new View('exception');
            $view->exception = $ex;

            $response = new Response();

            $response->setResponseCode(500);
            $response->setContent($view->render());
        }

        return $response;
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
        $controller = new $class($this->configuration, $this->request);

        $controller->init();

        return $controller;
    }

    /**
     * Check whether we should skip auth (because it is disabled)
     *
     * @return bool
     */
    protected function shouldSkipAuth(): bool
    {
        $disableAuth   = (bool)$this->configuration->get('php-censor.security.disable_auth', false);
        $defaultUserId = (int)$this->configuration->get('php-censor.security.default_user_id', 1);

        if ($disableAuth && $defaultUserId) {
            $user = $this->storeRegistry->get('User')->getByPrimaryKey($defaultUserId);

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
     * @return bool
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
