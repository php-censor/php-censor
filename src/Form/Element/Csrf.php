<?php

declare(strict_types=1);

namespace PHPCensor\Form\Element;

use PHPCensor\View;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Csrf extends Hidden
{
    private Session $session;

    public function __construct(Session $session, ?string $name = null)
    {
        parent::__construct($name);

        $this->session = $session;
    }

    public function validate(): bool
    {
        $tokens = $this->session->get('csrf_tokens');
        $sessionToken = isset($tokens[$this->getName()]) ? $tokens[$this->getName()] : null;

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

        $tokens = $this->session->get('csrf_tokens');
        $tokens[$this->getName()] = $this->getValue();

        $this->session->set('csrf_tokens', $tokens);
    }
}
