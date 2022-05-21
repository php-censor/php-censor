<?php

declare(strict_types=1);

namespace PHPCensor;

use Exception;
use PHPCensor\Exception\HttpException;
use PHPCensor\Exception\HttpException\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PHPCensor\Http\Router;
use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;
use PHPCensor\Common\Application\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Application
{
    private ?array $route;

    private Controller $controller;

    private Request $request;

    private Session $session;

    private ConfigurationInterface $configuration;

    private StoreRegistry $storeRegistry;

    private UserStore $userStore;

    private Router $router;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        UserStore $userStore,
        Request $request,
        Session $session
    ) {
        $this->configuration = $configuration;
        $this->storeRegistry = $storeRegistry;
        $this->userStore     = $userStore;
        $this->request       = $request;
        $this->session       = $session;

        $this->router = new Router($this, $this->request);

        $this->init();
    }

    /**
     * Initialise Application - Handles session verification, routing, etc.
     */
    public function init(): void
    {
        $request =& $this->request;
        $route   = '/:controller/:action';
        $opts    = ['controller' => 'Home', 'action' => 'index'];

        $session = $this->session;

        // Inlined as a closure to fix "using $this when not in object context" on 5.3
        $validateSession = function () use ($session) {
            $sessionUserId = $session->get('php-censor-user-id');
            if (!empty($sessionUserId)) {
                $user = $this->userStore->getById((int)$sessionUserId);

                if ($user) {
                    return true;
                }
            }

            return false;
        };

        $skipAuth = [$this, 'shouldSkipAuth'];

        // Handler for the route we're about to register, checks for a valid session where necessary:
        $routeHandler = function ($route, Response &$response) use (&$request, $validateSession, $skipAuth, $session) {
            $skipValidation = \in_array($route['controller'], ['session', 'webhook', 'build-status'], true);

            if (!$skipValidation && !$validateSession() && (!\is_callable($skipAuth) || !$skipAuth())) {
                if ($request->isXmlHttpRequest()) {
                    $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    $response->setContent(null);
                } else {
                    $session->set('php-censor-login-redirect', \substr($request->getPathInfo(), 1));

                    $response = new RedirectResponse(APP_URL . 'session/login');
                }

                return false;
            }

            return true;
        };

        $this->router->clearRoutes();
        $this->router->register($route, $opts, $routeHandler);
    }

    /**
     * @throws NotFoundException
     */
    protected function handleRequestInner(): Response
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
            throw new NotFoundException(
                'Controller ' . $this->toPhpName($this->route['controller']) . ' does not exist!'
            );
        }

        $action = \lcfirst($this->toPhpName($this->route['action']));
        if (!$this->getController()->hasAction($action)) {
            throw new NotFoundException(
                'Controller ' . $this->toPhpName($this->route['controller']) . ' does not have action ' . $action . '!'
            );
        }

        return $this->getController()->handleAction($action, $this->route['args']);
    }

    /**
     * @throws Common\Exception\RuntimeException
     * @throws HttpException
     */
    private function getUser(): ?User
    {
        $sessionUserId = $this->session->get('php-censor-user-id');
        if (empty($sessionUserId)) {
            return null;
        }

        /** @var ?User $user */
        $user = $this->userStore->getById((int)$sessionUserId);

        return $user;
    }

    /**
     * Handle an incoming web request.
     *
     * @throws Common\Exception\RuntimeException
     * @throws HttpException
     */
    public function handleRequest(): Response
    {
        try {
            $response = $this->handleRequestInner();
        } catch (HttpException $ex) {
            $view = new View('exception');
            $view->exception = $ex;
            $view->user      = $this->getUser();

            $response = new Response();

            $response->setStatusCode($ex->getErrorCode());
            $response->setContent($view->render());
        } catch (\Throwable $ex) {
            $view = new View('exception');
            $view->exception = $ex;

            $response = new Response();

            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($view->render());
        }

        return $response;
    }

    /**
     * Loads a particular controller, and injects our layout view into it.
     */
    protected function loadController(string $class): Controller
    {
        /** @var Controller $controller */
        $controller = new $class($this->configuration, $this->storeRegistry, $this->request, $this->session);

        $controller->init();

        return $controller;
    }

    /**
     * Check whether we should skip auth (because it is disabled)
     *
     * @throws Common\Exception\RuntimeException
     */
    protected function shouldSkipAuth(): bool
    {
        $disableAuth   = (bool)$this->configuration->get('php-censor.security.disable_auth', false);
        $defaultUserId = (int)$this->configuration->get('php-censor.security.default_user_id', 1);

        if ($disableAuth && $defaultUserId) {
            $user = $this->userStore->getById($defaultUserId);

            if ($user) {
                return true;
            }
        }

        return false;
    }

    public function getController(): Controller
    {
        if (empty($this->controller)) {
            $controllerClass  = $this->getControllerClass($this->route);
            $this->controller = $this->loadController($controllerClass);
        }

        return $this->controller;
    }

    protected function controllerExists(array $route): bool
    {
        return \class_exists($this->getControllerClass($route));
    }

    protected function getControllerClass(array $route): string
    {
        $controller = $this->toPhpName($route['controller']);

        return 'PHPCensor\Controller\\' . $controller . 'Controller';
    }

    public function isValidRoute(array $route): bool
    {
        if ($this->controllerExists($route)) {
            return true;
        }

        return false;
    }

    protected function toPhpName(string $string): string
    {
        $string = \str_replace('-', ' ', $string);
        $string = \ucwords($string);

        return \str_replace(' ', '', $string);
    }
}
