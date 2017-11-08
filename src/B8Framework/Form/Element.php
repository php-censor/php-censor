<?php

namespace b8\Form;

use b8\View;
use b8\Config;

abstract class Element
{
    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_label;

    /**
     * @var string
     */
    protected $_css;

    /**
     * @var string
     */
    protected $_ccss;

    /**
     * @var Element
     */
    protected $_parent;

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
        return $this->_name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = strtolower(preg_replace('/([^a-zA-Z0-9_\-%])/', '', $name));
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return !$this->_id ? 'element-' . $this->_name : $this->_id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->_css;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->_css = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getContainerClass()
    {
        return $this->_ccss;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setContainerClass($class)
    {
        $this->_ccss = $class;
        return $this;
    }

    /**
     * @param Element $parent
     *
     * @return $this
     */
    public function setParent(Element $parent)
    {
        $this->_parent = $parent;
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

        $view->name   = $this->getName();
        $view->id     = $this->getId();
        $view->label  = $this->getLabel();
        $view->css    = $this->getClass();
        $view->ccss   = $this->getContainerClass();
        $view->parent = $this->_parent;

        $this->onPreRender($view);

        return $view->render();
    }

    /**
     * @param View $view
     */
    abstract protected function onPreRender(View &$view);
}
