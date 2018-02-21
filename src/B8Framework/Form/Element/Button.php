<?php

namespace b8\Form\Element;

use b8\Form\Input;
use PHPCensor\View;

class Button extends Input
{
    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'button';
    }
}
