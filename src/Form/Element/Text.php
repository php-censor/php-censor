<?php

namespace PHPCensor\Form\Element;

use PHPCensor\Form\Input;
use PHPCensor\View;

class Text extends Input
{
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'text';
    }
}
