<?php

namespace b8\Form\Element;

use PHPCensor\View;

class Url extends Text
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

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'url';
    }
}
