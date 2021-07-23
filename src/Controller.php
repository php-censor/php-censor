<?php

declare(strict_types = 1);

namespace PHPCensor;

use PHPCensor\Http\Request;
use PHPCensor\Http\Response;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class Controller
{
    protected Request $request;

    protected ConfigurationInterface $configuration;

    protected StoreRegistry $storeRegistry;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        Request $request
    ) {
        $this->configuration = $configuration;
        $this->storeRegistry = $storeRegistry;
        $this->request       = $request;
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

        if (\method_exists($this, '__call')) {
            return true;
        }

        return false;
    }

    /**
     * Handles an action on this controller and returns a Response object.
     *
     * @param string $action
     * @param array  $actionParams
     *
     * @return Response
     */
    public function handleAction(string $action, array $actionParams): Response
    {
        return \call_user_func_array([$this, $action], $actionParams);
    }

    /**
     * Get a hash of incoming request parameters ($_GET, $_POST)
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->request->getParams();
    }

    /**
     * Get a specific incoming request parameter.
     *
     * @param string $key
     * @param mixed  $default Default return value (if key does not exist)
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->request->getParam($key, $default);
    }

    /**
     * Change the value of an incoming request parameter.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParam(string $key, $value)
    {
        $this->request->setParam($key, $value);
    }

    /**
     * Remove an incoming request parameter.
     *
     * @param string $key
     */
    public function unsetParam(string $key): void
    {
        $this->request->unsetParam($key);
    }
}
