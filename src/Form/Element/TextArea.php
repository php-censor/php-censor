<?php

declare(strict_types = 1);

namespace PHPCensor\Form\Element;

use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class TextArea extends Text
{
    protected int $rows = 4;

    public function getRows(): int
    {
        return $this->rows;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $view->rows = $this->getRows();
    }
}
