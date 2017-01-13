<?php

namespace b8\Form\Element;

use b8\View;

class Email extends Text
{
    public function render($viewFile = null)
    {
        return parent::render(($viewFile ? $viewFile : 'Text'));
    }

    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);
        $view->type = 'email';
    }
}
