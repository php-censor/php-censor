<?php

namespace b8\Form\Element;

use PHPCensor\View;

class Submit extends Button
{
    /**
     * @var string
     */
    protected $value = 'Submit';

    /**
     * @param string $viewFile
     *
     * @return string
     */
    public function render($viewFile = null)
    {
        return parent::render(($viewFile ? $viewFile : 'Button'));
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->type = 'submit';
    }
}
