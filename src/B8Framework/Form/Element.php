<?php

namespace b8\Form;

use PHPCensor\View;
use b8\Config;

abstract class Element
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $containerClass;

    /**
     * @var Element
     */
    protected $parent;

    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        if (!is_null($name)) {
            $this->setName($name);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = strtolower(preg_replace('/([^a-zA-Z0-9_\-%])/', '', $name));

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return !$this->id
            ? ('element-' . $this->name)
            : $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getContainerClass()
    {
        return $this->containerClass;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setContainerClass($class)
    {
        $this->containerClass = $class;

        return $this;
    }

    /**
     * @param Element $parent
     *
     * @return $this
     */
    public function setParent(Element $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param string $viewFile
     *
     * @return string
     */
    public function render($viewFile = null)
    {
        $viewPath = Config::getInstance()->get('b8.view.path');

        if (is_null($viewFile)) {
            $class    = explode('\\', get_called_class());
            $viewFile = end($class);
        }

        if (file_exists($viewPath . 'Form/' . $viewFile . '.phtml')) {
            $view = new View('Form/' . $viewFile);
        } else {
            $view = new View($viewFile, B8_PATH . 'Form/View/');
        }

        $view->name           = $this->getName();
        $view->id             = $this->getId();
        $view->label          = $this->getLabel();
        $view->class          = $this->getClass();
        $view->containerClass = $this->getContainerClass();
        $view->parent         = $this->parent;

        $this->onPreRender($view);

        return $view->render();
    }

    /**
     * @param View $view
     */
    abstract protected function onPreRender(View &$view);
}
