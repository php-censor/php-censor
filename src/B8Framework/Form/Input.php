<?php

namespace b8\Form;

use b8\View;

class Input extends Element
{
    /**
     * @var boolean
     */
    protected $_required = false;

    /**
     * @var string
     */
    protected $_pattern;

    /**
     * @var callable
     */
    protected $_validator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $_error;

    /**
     * @var boolean
     */
    protected $_customError = false;

    /**
     * @param string  $name
     * @param string  $label
     * @param boolean $required
     *
     * @return static
     */
    public static function create($name, $label, $required = false)
    {
        $el = new static();
        $el->setName($name);
        $el->setLabel($label);
        $el->setRequired($required);

        return $el;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->_required;
    }

    /**
     * @param boolean $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->_required = (bool)$required;

        return $this;
    }

    /**
     * @return callable
     */
    public function getValidator()
    {
        return $this->_validator;
    }

    /**
     * @param callable $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        if (is_callable($validator) || $validator instanceof \Closure) {
            $this->_validator = $validator;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * @param string $pattern
     *
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;

        return $this;
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        if ($this->getRequired() && empty($this->value)) {
            $this->_error = $this->getLabel() . ' is required.';
            return false;
        }

        if ($this->getPattern() && !preg_match('/' . $this->getPattern() . '/', $this->value)) {
            $this->_error = 'Invalid value entered.';

            return false;
        }

        $validator = $this->getValidator();

        if (is_callable($validator)) {
            try {
                call_user_func_array($validator, [$this->value]);
            } catch (\Exception $ex) {
                $this->_error = $ex->getMessage();

                return false;
            }
        }

        if ($this->_customError) {
            return false;
        }

        return true;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setError($message)
    {
        $this->_customError = true;
        $this->_error       = $message;

        return $this;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        $view->value    = $this->getValue();
        $view->error    = $this->_error;
        $view->pattern  = $this->_pattern;
        $view->required = $this->_required;
    }
}
