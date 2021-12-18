<?php

namespace PHPCensor\Form\Element;

use PHPCensor\Form\Input;
use PHPCensor\View;

class Select extends Input
{
    /**
     * @var array
     */
    protected $options = [];

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->options = $this->options;
    }
}
