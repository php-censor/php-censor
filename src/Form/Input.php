<?php

declare(strict_types = 1);

namespace PHPCensor\Form;

use Closure;
use Exception;
use PHPCensor\Form\DataTransformer\DataTransformerInterface;
use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Input extends Element
{
    protected bool $required = false;

    protected string $pattern = '';

    /**
     * @var callable
     */
    protected $validator;

    /**
     * @var mixed
     */
    protected $value;

    protected string $error = '';

    protected bool $customError = false;

    protected ?DataTransformerInterface $dataTransformer = null;

    public static function create(string $name, string $label, bool $required = false): self
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
        if (!empty($this->getDataTransformer())) {
            return $this->getDataTransformer()->reverseTransform((string)$this->value);
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        if (!empty($this->getDataTransformer())) {
            $this->value = $this->getDataTransformer()->transform($value);
        } else {
            $this->value = $value;
        }

        return $this;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return callable
     */
    public function getValidator()
    {
        return $this->validator;
    }

    public function setValidator($validator): self
    {
        if (\is_callable($validator) || $validator instanceof Closure) {
            $this->validator = $validator;
        }

        return $this;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function validate(): bool
    {
        if ($this->getRequired() && empty($this->getValue())) {
            $this->error = $this->getLabel() . ' is required.';
            return false;
        }

        if ($this->getPattern() && !\preg_match('/' . $this->getPattern() . '/', (string)$this->getValue())) {
            $this->error = 'Invalid value entered.';

            return false;
        }

        $validator = $this->getValidator();

        if (\is_callable($validator)) {
            try {
                \call_user_func_array($validator, [$this->getValue()]);
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

    public function setError(string $message): self
    {
        $this->customError = true;
        $this->error       = $message;

        return $this;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view): void
    {
        $view->value    = $this->getValue();
        $view->error    = $this->error;
        $view->pattern  = $this->pattern;
        $view->required = $this->required;
    }

    public function getDataTransformer(): ?DataTransformerInterface
    {
        return $this->dataTransformer;
    }

    public function setDataTransformer(DataTransformerInterface $dataTransformer): self
    {
        $this->dataTransformer = $dataTransformer;

        return $this;
    }
}
