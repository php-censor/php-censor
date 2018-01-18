<?php

namespace b8;

use b8\Exception\HttpException\NotFoundException;
use b8\Http\Response;
use b8\Http\Request;
use b8\Http\Router;

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
     * @return Response
     *
     * @throws NotFoundException
     */
    public function handleRequest()
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
     * @param string $class
     *
     * @return Controller
     */
    protected function loadController($class)
    {
        /** @var Controller $controller */
        $controller = new $class($this->config, $this->request, $this->response);
        $controller->init();

        return $controller;
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

        return $this->config->get('b8.app.namespace') . '\\' . $namespace . '\\' . $controller . 'Controller';
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
