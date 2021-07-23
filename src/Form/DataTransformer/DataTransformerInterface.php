<?php

declare(strict_types = 1);

namespace PHPCensor\Form\DataTransformer;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface DataTransformerInterface
{
    /** transform when putting the data into a form */
    public function transform(string $value): string;

    /** reversen when getting the data from a form */
    public function reverseTransform(string $value): string;
}
