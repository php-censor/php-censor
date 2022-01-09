<?php

declare(strict_types=1);

namespace PHPCensor\Form\Element;

use PHPCensor\View;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Csrf extends Hidden
{
    public function validate(): bool
    {
        $sessionToken = isset($_SESSION['csrf_tokens'][$this->getName()])
            ? $_SESSION['csrf_tokens'][$this->getName()]
            : null;

        if ($this->value !== $sessionToken) {
            return false;
        }

        return true;
    }

    protected function onPreRender(View &$view): void
    {
        parent::onPreRender($view);

        $this->setValue(
            \rtrim(\strtr(\base64_encode(\random_bytes(32)), '+/', '-_'), '=')
        );

        $view->value = $this->getValue();

        $_SESSION['csrf_tokens'][$this->getName()] = $this->getValue();
    }
}
