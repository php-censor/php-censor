<?php

namespace PHPCensor\Form\Element;

use PHPCensor\Form\Input;
use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Button extends Input
{
    /**
     * @return bool
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
