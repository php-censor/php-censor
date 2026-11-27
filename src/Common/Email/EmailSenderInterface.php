<?php

declare(strict_types=1);

namespace PHPCensor\Common\Email;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface EmailSenderInterface
{
    public function send(EmailInterface $email, bool $verbose = false): bool;
}
