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
class Checkbox extends Input
{
    protected bool $checked = false;

    /**
     * @var mixed
     */
    protected $checkedValue;

    /**
     * @return mixed
     */
    public function getCheckedValue()
    {
        return $this->checkedValue;
    }

    /**
     * @param mixed $value
     */
    public function setCheckedValue($value): self
    {
        $this->checkedValue = $value;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): self
    {
        if (\is_bool($value) && $value === true) {
            $this->value   = $this->getCheckedValue();
            $this->checked = true;

            return $this;
        }

        if ($value === $this->getCheckedValue()) {
            $this->value   = $this->getCheckedValue();
            $this->checked = true;

            return $this;
        }

        $this->value   = $value;
        $this->checked = false;

        return $this;
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $view->checkedValue = $this->getCheckedValue();
        $view->checked      = $this->checked;
    }
}
