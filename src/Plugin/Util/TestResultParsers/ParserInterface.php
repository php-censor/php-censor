<?php

namespace PHPCensor\Plugin\Util\TestResultParsers;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ParserInterface
{
    /**
     * @return array An array of key/value pairs for storage in the plugins result metadata
     */
    public function parse();

    public function getTotalTests();
    public function getTotalTimeTaken();
    public function getTotalFailures();
}
