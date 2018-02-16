<?php

namespace b8\Form;

use b8\View;

class Input extends Element
{
    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var callable
     */
    protected $validator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var boolean
     */
    protected $customError = false;

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
        return $this->required;
    }

    /**
     * @param boolean $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = (bool)$required;

        return $this;
    }

    /**
     * @return callable
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param callable $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        if (is_callable($validator) || $validator instanceof \Closure) {
            $this->validator = $validator;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     *
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        if ($this->getRequired() && empty($this->value)) {
            $this->error = $this->getLabel() . ' is required.';
            return false;
        }

        if ($this->getPattern() && !preg_match('/' . $this->getPattern() . '/', $this->value)) {
            $this->error = 'Invalid value entered.';

            return false;
        }

        $validator = $this->getValidator();

        if (is_callable($validator)) {
            try {
                call_user_func_array($validator, [$this->value]);
            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();

                return false;
            }
        }

        if ($this->customError) {
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
        $this->customError = true;
        $this->error       = $message;

        return $this;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        $view->value    = $this->getValue();
        $view->error    = $this->error;
        $view->pattern  = $this->pattern;
        $view->required = $this->required;
    }
}
