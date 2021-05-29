<?php

namespace PHPCensor\Form\Validator;

use PHPCensor\Common\Exception\RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class Yaml implements ValidatorInterface
{
    /** @var Parser */
    protected $parser;

    public function __invoke($value)
    {
        try {
            $this->getParser()->parse($value);
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return true;
    }

    public function getParser()
    {
        if (!$this->parser) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }
}
