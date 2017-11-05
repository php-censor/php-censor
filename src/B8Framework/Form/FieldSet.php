<?php

namespace b8\Form;

use b8\View;

class FieldSet extends Element
{
    /**
     * @var Element[]
     */
    protected $_children = [];

    /**
     * @return array
     */
    public function getValues()
    {
        $rtn = [];
        foreach ($this->_children as $field) {
            if ($field instanceof FieldSet) {
                $fieldName = $field->getName();

                if (empty($fieldName)) {
                    $rtn = array_merge($rtn, $field->getValues());
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

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($this->_children as $field) {
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
    }

    /**
     * @param Element $field
     */
    public function addField(Element $field)
    {
        $this->_children[$field->getName()] = $field;
        $field->setParent($this);
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        $rtn = true;

        foreach ($this->_children as $child) {
            if (!$child->validate()) {
                $rtn = false;
            }
        }

        return $rtn;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        $rendered = [];
        foreach ($this->_children as $child) {
            $rendered[] = $child->render();
        }

        $view->children = $rendered;
    }

    /**
     * @return Element[]
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * @param string $fieldName
     *
     * @return Element
     */
    public function getChild($fieldName)
    {
        return $this->_children[$fieldName];
    }
}
