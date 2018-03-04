<?php

namespace PHPCensor;

use b8\Exception\HttpException\ForbiddenException;
use b8\Http\Request;
use b8\Http\Response;
use PHPCensor\Store\Factory;
use PHPCensor\Model\User;
use PHPCensor\Store\UserStore;

class Controller
{
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
     * @var View
     */
    protected $controllerView;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var View
     */
    public $layout;

    /**
     * Initialise the controller.
     */
    public function init()
    {
        // Extended by actual controllers.
    }

    /**
     * @param Config   $config
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Config $config, Request $request, Response $response)
    {
        $this->config   = $config;
        $this->request  = $request;
        $this->response = $response;

        $class = explode('\\', get_class($this));
        $this->className = substr(array_pop($class), 0, -10);
        $this->setControllerView();

        if (!empty($_SESSION['php-censor-user'])) {
            unset($_SESSION['php-censor-user']);
        }
    }

    /**
     * Set the view that this controller should use.
     */
    protected function setControllerView()
    {
        if (View::exists($this->className)) {
            $this->controllerView = new View($this->className);
        } else {
            $this->controllerView = new View('{@content}');
        }
    }

    /**
     * Set the view that this controller action should use.
     *
     * @param string $action
     */
    protected function setView($action)
    {
        if (View::exists($this->className . '/' . $action)) {
            $this->view = new View($this->className . '/' . $action);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param string $action
     * @param array  $actionParams
     *
     * @return Response
     */
    public function handleAction($action, $actionParams)
    {
        $this->setView($action);
        $response = call_user_func_array([$this, $action], $actionParams);

        if ($response instanceof Response) {
            return $response;
        }

        if (is_string($response)) {
            $this->controllerView->content = $response;
        } elseif (isset($this->view)) {
            $this->controllerView->content = $this->view->render();
        }

        $this->response->setContent($this->controllerView->render());

        return $this->response;
    }

    /**
     * Require that the currently logged in user is an administrator.
     *
     * @throws ForbiddenException
     */
    protected function requireAdmin()
    {
        if (!$this->currentUserIsAdmin()) {
            throw new ForbiddenException('You do not have permission to do that.');
        }
    }

    /**
     * Check if the currently logged in user is an administrator.
     *
     * @return boolean
     */
    protected function currentUserIsAdmin()
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        return $this->getUser()->getIsAdmin();
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        if (empty($_SESSION['php-censor-user-id'])) {
            return null;
        }

        /** @var UserStore $userStore */
        $userStore = Factory::getStore('User');

        return $userStore->getById($_SESSION['php-censor-user-id']);
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasAction($name)
    {
        if (method_exists($this, $name)) {
            return true;
        }

        if (method_exists($this, '__call')) {
            return true;
        }

        return false;
    }

    /**
     * Get a hash of incoming request parameters ($_GET, $_POST)
     *
     * @return array
     */
    public function getParams()
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
    public function getParam($key, $default = null)
    {
        return $this->request->getParam($key, $default);
    }

    /**
     * Change the value of an incoming request parameter.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value)
    {
        $this->request->setParam($key, $value);
    }

    /**
     * Remove an incoming request parameter.
     *
     * @param string $key
     */
    public function unsetParam($key)
    {
        $this->request->unsetParam($key);
    }
}
