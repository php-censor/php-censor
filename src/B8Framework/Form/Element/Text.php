<?php

namespace b8\Form\Element;

use b8\Form\Input;
use b8\View;

class Text extends Input
{
    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'text';
    }
}
