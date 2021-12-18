<?php

namespace PHPCensor\Form\Element;

use PHPCensor\Form\Input;
use PHPCensor\View;

class Button extends Input
{
    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'button';
    }
}
