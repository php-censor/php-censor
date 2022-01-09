<?php

declare(strict_types=1);

namespace PHPCensor\Form\Element;

use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Submit extends Button
{
    /**
     * @var string
     */
    protected $value = 'Submit';

    public function render(?string $viewFile = null): string
    {
        return parent::render(($viewFile ? $viewFile : 'Button'));
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $view->type = 'submit';
    }
}
