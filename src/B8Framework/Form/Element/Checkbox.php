<?php

namespace b8\Form\Element;

use PHPCensor\View;
use b8\Form\Input;

class Checkbox extends Input
{
    /**
     * @var boolean
     */
    protected $checked;

    /**
     * @var mixed
     */
    protected $checkedValue;

    /**
     * @return mixed
     */
    public function getCheckedValue()
    {
        return $this->checkedValue;
    }

    /**
     * @param mixed $value
     */
    public function setCheckedValue($value)
    {
        $this->checkedValue = $value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        if (is_bool($value) && $value === true) {
            $this->value   = $this->getCheckedValue();
            $this->checked = true;
            return;
        }

        if ($value == $this->getCheckedValue()) {
            $this->value   = $this->getCheckedValue();
            $this->checked = true;
            return;
        }

        $this->value   = $value;
        $this->checked = false;
    }

    /**
     * @param View $view
     */
    public function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->checkedValue = $this->getCheckedValue();
        $view->checked      = $this->checked;
    }
}
