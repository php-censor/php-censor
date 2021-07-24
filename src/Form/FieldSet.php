<?php

declare(strict_types = 1);

namespace PHPCensor\Form;

use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class FieldSet extends Element
{
    /**
     * @var Element[]
     */
    protected array $children = [];

    public function getValues(): array
    {
        $rtn = [];
        foreach ($this->children as $field) {
            if ($field instanceof FieldSet) {
                $fieldName = $field->getName();

                if (empty($fieldName)) {
                    $rtn = \array_merge($rtn, $field->getValues());
                } else {
                    $rtn[$fieldName] = $field->getValues();
                }
            } elseif ($field instanceof Input) {
                if ($field->getName()) {
                    $rtn[$field->getName()] = $field->getValue();
                }
            }
        }

        return $rtn;
    }

    public function setValues(array $values): self
    {
        foreach ($this->children as $field) {
            if ($field instanceof FieldSet) {
                $fieldName = $field->getName();

                if (empty($fieldName) || !isset($values[$fieldName])) {
                    $field->setValues($values);
                } else {
                    $field->setValues($values[$fieldName]);
                }
            } elseif ($field instanceof Input) {
                $fieldName = $field->getName();

                if (isset($values[$fieldName])) {
                    $field->setValue($values[$fieldName]);
                }
            }
        }

        return $this;
    }

    public function addField(Element $field): self
    {
        $this->children[$field->getName()] = $field;
        $field->setParent($this);

        return $this;
    }

    public function validate(): bool
    {
        $rtn = true;

        foreach ($this->children as $child) {
            if (!$child->validate()) {
                $rtn = false;
            }
        }

        return $rtn;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view): void
    {
        $rendered = [];
        foreach ($this->children as $child) {
            $rendered[] = $child->render();
        }

        $view->children = $rendered;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChild(string $fieldName): Element
    {
        return $this->children[$fieldName];
    }
}
