<?php

namespace b8\Form\Element;

use PHPCensor\View;

class Csrf extends Hidden
{
    /**
     * @var integer
     */
    protected $rows = 4;

    /**
     * @return boolean
     */
    public function validate()
    {
        if ($this->value != $_COOKIE[$this->getName()]) {
            return false;
        }

        return true;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $csrf       = md5(microtime(true));
        $view->csrf = $csrf;

        setcookie($this->getName(), $csrf);
    }
}
