<?php

declare(strict_types=1);

namespace PHPCensor\Form\Validator;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ValidatorInterface
{
    public function __invoke($value): bool;
}
