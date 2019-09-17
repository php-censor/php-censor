<?php

namespace PHPCensor\Form\Validator;

use Exception;
use PHPCensor\Form\ValidatorInterface;
use PHPCensor\Helper\Lang;
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
            throw new Exception(Lang::get('error_yml_parse'));
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
