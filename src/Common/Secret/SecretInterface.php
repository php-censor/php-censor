<?php

declare(strict_types=1);

namespace PHPCensor\Common\Secret;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface SecretInterface
{
    public function getName(): ?string;

    public function getValue(): ?string;
}
