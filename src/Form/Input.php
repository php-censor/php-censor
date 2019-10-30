<?php

namespace PHPCensor\Form;

use Closure;
use Exception;
use PHPCensor\View;
use Symfony\Component\Form\DataTransformerInterface;

class Input extends Element
{
    /**
     * @var bool
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
     * @var bool
     */
    protected $customError = false;

    /** @var DataTransformerInterface */
    protected $dataTransformator;

    /**
     * @param string  $name
     * @param string  $label
     * @param bool $required
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
        if (!empty($this->getDataTransformator())) {
            return $this->getDataTransformator()->reverseTransform($this->value);
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        if (!empty($this->getDataTransformator())) {
            $this->value = $this->getDataTransformator()->transform($value);
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
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
        if (is_callable($validator) || $validator instanceof Closure) {
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
     * @return bool
     */
    public function validate()
    {
        if ($this->getRequired() && empty($this->getValue())) {
            $this->error = $this->getLabel() . ' is required.';
            return false;
        }

        if ($this->getPattern() && !preg_match('/' . $this->getPattern() . '/', $this->getValue())) {
            $this->error = 'Invalid value entered.';

            return false;
        }

        $validator = $this->getValidator();

        if (is_callable($validator)) {
            try {
                call_user_func_array($validator, [$this->getValue()]);
            } catch (Exception $ex) {
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

    /**
     * @return DataTransformerInterface
     */
    public function getDataTransformator()
    {
        return $this->dataTransformator;
    }

    /**
     * @param DataTransformerInterface $dataTransformator
     */
    public function setDataTransformator($dataTransformator)
    {
        $this->dataTransformator = $dataTransformator;
    }
}
