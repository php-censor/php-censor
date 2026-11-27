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
    public function __construct(
        protected ConfigurationInterface $configuration,
        protected StoreRegistry $storeRegistry,
        protected Request $request,
        protected Session $session
    ) {
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
     * Handles an action on this controller and returns a Response object (or a string, or an array of data for
     * the webhooks, see WebhookController::handleAction()).
     */
    public function handleAction(string $action, array $actionParams): Response|string|array
    {
        $result = \call_user_func_array([$this, $action], $actionParams);
        if (!$result) {
            $result = '';
        }

        return $result;
    }

    /**
     * Get a specific incoming request parameter.
     *
     * @param mixed  $default Default return value (if key does not exist)
     *
     * @return mixed
     */
    public function getParam(string $key, mixed $default = null)
    {
        // Same lookup order as the former Request::get() (removed in Symfony 8): route attributes, query, then body.
        // all() is used on purpose: InputBag::get() rejects non-scalar values (e.g. "field[]" arrays).
        foreach ([$this->request->attributes, $this->request->query, $this->request->request] as $parameters) {
            if ($parameters->has($key)) {
                return $parameters->all()[$key];
            }
        }

        return $default;
    }
}
