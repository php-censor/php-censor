<?php

namespace PHPCensor\Form;

interface ValidatorInterface
{
    public function __invoke($value);
}
