<?php

namespace PHPCensor\Plugin\Util;

use Exception;

/**
 * Class PhpUnitResult parses the results for the PhpUnitV2 plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class PhpUnitResult
{
    final public const SEVERITY_PASS    = 'success';
    final public const SEVERITY_FAIL    = 'fail';
    final public const SEVERITY_ERROR   = 'error';
    final public const SEVERITY_SKIPPED = 'skipped';
    final public const SEVERITY_WARN    = self::SEVERITY_PASS;
    final public const SEVERITY_RISKY   = self::SEVERITY_PASS;
    protected $results;
    protected $failures = 0;
    protected $errors = [];

    public function __construct(protected $outputFile, protected $buildPath = '')
    {
    }

    /**
     * Parse the results
     *
     * @return $this
     * @throws Exception If fails to parse the output
     */
    abstract public function parse();

    abstract protected function getSeverity($testCase);

    abstract protected function buildMessage($testCase);

    abstract protected function buildTrace($testCase);

    abstract protected function getFileAndLine($testCase);

    protected function getOutput($testCase)
    {
        return $testCase['output'];
    }

    protected function parseTestcase($testcase)
    {
        $severity = $this->getSeverity($testcase);
        $pass = isset(\array_fill_keys([self::SEVERITY_PASS, self::SEVERITY_SKIPPED], true)[$severity]);
        $data = [
            'pass'     => $pass,
            'severity' => $severity,
            'message'  => $this->buildMessage($testcase),
            'trace'    => $pass ? [] : $this->buildTrace($testcase),
            'output'   => $this->getOutput($testcase),
        ];

        if (!$pass) {
            $this->failures++;
            $info = $this->getFileAndLine($testcase);
            $this->errors[] = [
                'message'  => $data['message'],
                'severity' => $severity,
                'file'     => $info['file'],
                'line'     => $info['line'],
            ];
        }

        $this->results[] = $data;
    }

    /**
     * Get the parse results
     *
     * @return string[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get the total number of failing tests
     *
     * @return int
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Get the tests with failing status
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
