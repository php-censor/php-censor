<?php

namespace b8;

use b8\Form\FieldSet;
use PHPCensor\View;

class Form extends FieldSet
{
    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        $view->action = $this->getAction();
        $view->method = $this->getMethod();

        parent::onPreRender($view);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
