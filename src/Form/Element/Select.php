<?php

declare(strict_types=1);

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
class Select extends Input
{
    protected array $options = [];

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $view->options = $this->options;
    }
}
