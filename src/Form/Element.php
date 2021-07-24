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
abstract class Element
{
    protected string $name = '';

    protected string $id = '';

    protected string $label = '';

    protected string $class = '';

    protected string $containerClass = '';

    protected ?Element $parent = null;

    public function __construct(?string $name = null)
    {
        if (!\is_null($name)) {
            $this->setName($name);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = \strtolower(\preg_replace('/([^a-zA-Z0-9_\-%])/', '', $name));

        return $this;
    }

    public function getId(): string
    {
        return !$this->id
            ? ('element-' . $this->name)
            : $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getContainerClass(): string
    {
        return $this->containerClass;
    }

    public function setContainerClass(string $class): self
    {
        $this->containerClass = $class;

        return $this;
    }

    public function setParent(Element $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function render(?string $viewFile = null): string
    {
        if (\is_null($viewFile)) {
            $class    = \explode('\\', \get_called_class());
            $viewFile = \end($class);
        }

        $view = new View('Form/' . $viewFile);

        $view->name           = $this->getName();
        $view->id             = $this->getId();
        $view->label          = $this->getLabel();
        $view->class          = $this->getClass();
        $view->containerClass = $this->getContainerClass();
        $view->parent         = $this->parent;

        $this->onPreRender($view);

        return $view->render();
    }

    /**
     * @param View $view
     */
    abstract protected function onPreRender(View &$view): void;
}
