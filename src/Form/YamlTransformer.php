<?php

use Symfony\Component\Form\DataTransformerInterface;

class YamlTransformer implements DataTransformerInterface
{

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}
