<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Common\View\ViewInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class View implements ViewInterface
{
    protected array $data = [];

    protected string $viewFile;

    protected string $path = SRC_DIR . 'View/';

    protected string $extension = 'phtml';

    /**
     * @throws RuntimeException
     */
    public function __construct(string $file, ?string $path = null, ?string $fileExtension = null)
    {
        if ($fileExtension) {
            $this->extension = $fileExtension;
        }

        if ($path) {
            $this->path = $path;
        }

        if (!$this->exists($file)) {
            throw new RuntimeException('View file does not exist: ' . $file);
        }

        $this->viewFile = $this->getViewFile($file);
    }

    protected function getViewFile(string $file): string
    {
        return $this->path . $file . '.' . $this->extension;
    }

    public function exists(string $file): bool
    {
        if (!\file_exists($this->getViewFile($file))) {
            return false;
        }

        return true;
    }

    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key];
    }

    /**
     * @param mixed  $value
     */
    public function __set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function hasVariable(string $key): bool
    {
        return $this->__isset($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getVariable(string $key)
    {
        return $this->__get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function setVariable(string $key, $value): bool
    {
        $this->__set($key, $value);

        return true;
    }

    public function setVariables(array $values): bool
    {
        foreach ($values as $key => $value) {
            $this->setVariable($key, $value);
        }
    }

    public function render(): string
    {
        \extract($this->data);

        \ob_start();

        require($this->viewFile);

        $html = \ob_get_contents();
        \ob_end_clean();

        return $html;
    }
}
