<?php

declare(strict_types = 1);

namespace PHPCensor\Form\DataTransformer;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Yaml implements DataTransformerInterface
{
    public function transform(string $value): string
    {
        /* nothing to do here - only called before displaying values on FE */
        return $value;
    }

    public function reverseTransform(string $value): string
    {
        return \str_replace("\t", "    ", $value);
    }
}
