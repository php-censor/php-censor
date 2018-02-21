<?php

namespace b8\Form\Element;

use PHPCensor\View;
use b8\Form\Input;

class Select extends Input
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->options = $this->options;
    }
}
