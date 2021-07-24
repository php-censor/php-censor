<?php

declare(strict_types = 1);

namespace PHPCensor;

use PHPCensor\Common\Exception\RuntimeException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class View
{
    protected array $data = [];

    protected string $viewFile;

    protected static string $extension = 'phtml';

    /**
     * @param string      $file
     * @param string|null $path
     *
     * @throws RuntimeException
     */
    public function __construct(string $file, ?string $path = null)
    {
        if (!self::exists($file, $path)) {
            throw new RuntimeException('View file does not exist: ' . $file);
        }

        $this->viewFile = self::getViewFile($file, $path);
    }

    protected static function getViewFile(string $file, ?string $path = null): string
    {
        $viewPath = \is_null($path) ? (SRC_DIR . 'View/') : $path;

        return $viewPath . $file . '.' . static::$extension;
    }

    public static function exists(string $file, ?string $path = null): bool
    {
        if (!\file_exists(self::getViewFile($file, $path))) {
            return false;
        }

        return true;
    }

    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function __set(string $key, $value): void
    {
        $this->data[$key] = $value;
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
