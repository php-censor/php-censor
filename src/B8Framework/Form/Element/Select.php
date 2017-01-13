<?php

namespace b8\Form\Element;

use b8\View, b8\Form\Input;

class Select extends Input
{
    protected $_options = [];

    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);
        $view->options = $this->_options;
    }
}
