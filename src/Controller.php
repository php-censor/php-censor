<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Http\Request;
use PHPCensor\Http\Response;
use PHPCensor\Common\Application\ConfigurationInterface;

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
     * @return Response|string
     */
    public function handleAction(string $action, array $actionParams)
    {
        return \call_user_func_array([$this, $action], $actionParams);
    }

    /**
     * Get a hash of incoming request parameters ($_GET, $_POST)
     */
    public function getParams(): array
    {
        return $this->request->getParams();
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
        return $this->request->getParam($key, $default);
    }

    /**
     * Change the value of an incoming request parameter.
     *
     * @param mixed  $value
     */
    public function setParam(string $key, $value)
    {
        $this->request->setParam($key, $value);
    }

    /**
     * Remove an incoming request parameter.
     */
    public function unsetParam(string $key): void
    {
        $this->request->unsetParam($key);
    }
}
