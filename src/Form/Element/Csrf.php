<?php

namespace PHPCensor\Form\Element;

use PHPCensor\View;

class Csrf extends Hidden
{
    /**
     * @return boolean
     */
    public function validate()
    {
        $sessionToken = isset($_SESSION['csrf_tokens'][$this->getName()])
            ? $_SESSION['csrf_tokens'][$this->getName()]
            : null;

        if ($this->value !== $sessionToken) {
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

        $this->setValue(
            rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=')
        );

        $view->value = $this->getValue();

        $_SESSION['csrf_tokens'][$this->getName()] = $this->getValue();
    }
}
