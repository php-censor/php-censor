<?php

declare(strict_types = 1);

namespace PHPCensor\Form\Validator;

use PHPCensor\Common\Exception\RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Yaml implements ValidatorInterface
{
    protected ?Parser $parser = null;

    public function __invoke($value): bool
    {
        try {
            $this->getParser()->parse($value);
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return true;
    }

    public function getParser(): Parser
    {
        if (!$this->parser) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }
}
