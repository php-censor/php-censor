<?php

declare(strict_types=1);

namespace PHPCensor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPCensor\Common\Application\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class Controller
{
    protected string $className;

    protected Request $request;

    protected Session $session;

    protected ConfigurationInterface $configuration;

    protected StoreRegistry $storeRegistry;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        Request $request,
        Session $session
    ) {
        $this->configuration = $configuration;
        $this->storeRegistry = $storeRegistry;
        $this->request       = $request;
        $this->session       = $session;

        $class           = \explode('\\', \get_class($this));
        $this->className = \substr(\array_pop($class), 0, -10);
    }

    /**
     * Initialise the controller.
     */
    abstract public function init(): void;

    public function hasAction(string $name): bool
    {
        if (\method_exists($this, $name)) {
            return true;
        }

        return false;
    }

    /**
     * Handles an action on this controller and returns a Response object.
     *
     * @return Response|string
     */
    public function handleAction(string $action, array $actionParams)
    {
        return \call_user_func_array([$this, $action], $actionParams);
    }

    /**
     * Get a specific incoming request parameter.
     *
     * @param mixed  $default Default return value (if key does not exist)
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->request->get($key, $default);
    }
}
