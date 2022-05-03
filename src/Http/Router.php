<?php

namespace PHPCensor\Http;

use PHPCensor\Application;
use PHPCensor\Common\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Router
{
    protected Request $request;

    protected Application $application;

    protected array $routes = [
        ['route' => '/:controller/:action', 'callback' => null, 'defaults' => []]
    ];

    public function __construct(Application $application, Request $request)
    {
        $this->application = $application;
        $this->request     = $request;
    }

    public function clearRoutes()
    {
        $this->routes = [];
    }

    /**
     * @param string   $route Route definition
     * @param array    $options
     * @param callable $callback
     *
     * @throws InvalidArgumentException
     */
    public function register($route, $options = [], $callback = null)
    {
        if (!\is_callable($callback)) {
            throw new InvalidArgumentException('$callback must be callable.');
        }

        \array_unshift($this->routes, ['route' => $route, 'callback' => $callback, 'defaults' => $options]);
    }

    public function dispatch()
    {
        foreach ($this->routes as $route) {
            $pathParts = $this->request->getPathInfo();
            $pathParts = \array_values(\array_filter(\explode('/', $pathParts)));

            //-------
            // Set up default values for everything:
            //-------
            $thisNamespace  = 'Controller';
            $thisController = null;
            $thisAction     = null;

            if (\array_key_exists('namespace', $route['defaults'])) {
                $thisNamespace = $route['defaults']['namespace'];
            }

            if (\array_key_exists('controller', $route['defaults'])) {
                $thisController = $route['defaults']['controller'];
            }

            if (\array_key_exists('action', $route['defaults'])) {
                $thisAction = $route['defaults']['action'];
            }

            $routeParts = \array_filter(\explode('/', $route['route']));
            $routeMatches = true;

            while (\count($routeParts)) {
                $routePart = \array_shift($routeParts);
                $pathPart = \array_shift($pathParts);

                switch ($routePart) {
                    case ':namespace':
                        $thisNamespace = !\is_null($pathPart) ? $pathPart : $thisNamespace;
                        break;
                    case ':controller':
                        $thisController = !\is_null($pathPart) ? $pathPart : $thisController;
                        break;
                    case ':action':
                        $thisAction = !\is_null($pathPart) ? $pathPart : $thisAction;
                        break;
                    default:
                        if ($routePart != $pathPart) {
                            $routeMatches = false;
                        }
                }

                if (!$routeMatches || !\count($pathParts)) {
                    break;
                }
            }

            foreach ($pathParts as &$pathPart) {
                if (\is_numeric($pathPart)) {
                    $pathPart = (int)$pathPart;
                }
            }
            unset($pathPart);

            if ($routeMatches) {
                $route = [
                    'namespace'  => $thisNamespace,
                    'controller' => $thisController,
                    'action'     => $thisAction,
                    'args'       => $pathParts,
                    'callback'   => $route['callback']
                ];

                if ($this->application->isValidRoute($route)) {
                    return $route;
                }
            }
        }

        return null;
    }
}
