<?php

declare(strict_types = 1);

namespace PHPCensor;

use PHPCensor\Form\FieldSet;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Form extends FieldSet
{
    protected string $action = '';

    protected string $method = 'POST';

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    protected function onPreRender(View &$view): void
    {
        $view->action = $this->getAction();
        $view->method = $this->getMethod();

        parent::onPreRender($view);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
