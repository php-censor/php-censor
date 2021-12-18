<?php

namespace PHPCensor\Form\Element;

use PHPCensor\View;

class Email extends Text
{
    /**
     * @param string $viewFile
     *
     * @return string
     */
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
