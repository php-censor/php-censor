<?php
namespace PHPCensor\Form;

use Symfony\Component\Form\DataTransformerInterface;

class YamlTransformer implements DataTransformerInterface
{

    public function transform($value)
    {
        /* nothing to do here - only called before displaying values on FE */
        return $value;
    }

    public function reverseTransform($value)
    {
        return str_replace("\t", "    ", $value);
    }
}
