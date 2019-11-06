<?php

namespace PHPCensor\Form\Validator;

interface ValidatorInterface
{
    public function __invoke($value);
}
