<?php

namespace b8;

class View
{
    protected $vars             = [];
    protected static $helpers   = [];
    protected static $extension = 'phtml';

    public function __construct($file, $path = null)
    {
        if (!self::exists($file, $path)) {
            throw new \RuntimeException('View file does not exist: ' . $file);
        }

        $this->viewFile = self::getViewFile($file, $path);
    }

    protected static function getViewFile($file, $path = null)
    {
        $viewPath = is_null($path) ? Config::getInstance()->get('b8.view.path') : $path;
        $fullPath = $viewPath . $file . '.' . static::$extension;

        return $fullPath;
    }

    public static function exists($file, $path = null)
    {
        if (!file_exists(self::getViewFile($file, $path))) {
            return false;
        }

        return true;
    }

    public function __isset($var)
    {
        return isset($this->vars[$var]);
    }

    public function __get($var)
    {
        return $this->vars[$var];
    }

    public function __set($var, $val)
    {
        $this->vars[$var] = $val;
    }

    public function __call($method, $params = [])
    {
        if (!isset(self::$helpers[$method])) {
            $class = '\\' . Config::getInstance()->get('b8.app.namespace') . '\\Helper\\' . $method;

            if (!class_exists($class)) {
                $class = '\\b8\\View\\Helper\\' . $method;
            }

            if (!class_exists($class)) {
                throw new \Exception('Helper class does not exist: ' . $class);
            }

            self::$helpers[$method] = new $class();
        }

        return self::$helpers[$method];
    }

    public function render()
    {
        extract($this->vars);

        ob_start();
        require($this->viewFile);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
