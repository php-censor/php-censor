<?php

namespace b8\Form\Element;

use b8\View;
use b8\Form\Input;

class Checkbox extends Input
{
    /**
     * @var boolean
     */
    protected $_checked;

    /**
     * @var mixed
     */
    protected $_checkedValue;

    /**
     * @return mixed
     */
    public function getCheckedValue()
    {
        return $this->_checkedValue;
    }

    /**
     * @param mixed $value
     */
    public function setCheckedValue($value)
    {
        $this->_checkedValue = $value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        if (is_bool($value) && $value === true) {
            $this->_value   = $this->getCheckedValue();
            $this->_checked = true;
            return;
        }

        if ($value == $this->getCheckedValue()) {
            $this->_value   = $this->getCheckedValue();
            $this->_checked = true;
            return;
        }

        $this->_value   = $value;
        $this->_checked = false;
    }

    /**
     * @param View $view
     */
    public function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->checkedValue = $this->getCheckedValue();
        $view->checked      = $this->_checked;
    }
}
