<?php

namespace PHPCensor\Form\DataTransformer;

interface DataTransformerInterface
{
    /** transform when putting the data into a form */
    public function transform($value);

    /** reversen when getting the data from a form */
    public function reverseTransform($value);
}
