<?php

declare(strict_types = 1);

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
    public function validate(): bool
    {
        return true;
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $view->type = 'button';
    }
}
