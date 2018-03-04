<?php

namespace PHPCensor\Plugin\Util;

/**
 * Class PhpUnitResult parses the results for the PhpUnitV2 plugin
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
abstract class PhpUnitResult
{
    const SEVERITY_PASS    = 'success';
    const SEVERITY_FAIL    = 'fail';
    const SEVERITY_ERROR   = 'error';
    const SEVERITY_SKIPPED = 'skipped';
    const SEVERITY_WARN    = self::SEVERITY_PASS;
    const SEVERITY_RISKY   = self::SEVERITY_PASS;

    protected $outputFile;
    protected $buildPath;
    protected $results;
    protected $failures = 0;
    protected $errors = [];

    public function __construct($outputFile, $buildPath = '')
    {
        $this->outputFile = $outputFile;
        $this->buildPath  = $buildPath;
    }

    /**
     * Parse the results
     *
     * @return $this
     * @throws \Exception If fails to parse the output
     */
    abstract public function parse();

    abstract protected function getSeverity($testcase);

    abstract protected function buildMessage($testcase);

    abstract protected function buildTrace($testcase);

    protected function getFileAndLine($testcase)
    {
        return $testcase;
    }

    protected function getOutput($testcase)
    {
        return $testcase['output'];
    }

    protected function parseTestcase($testcase)
    {
        $severity = $this->getSeverity($testcase);
        $pass = isset(array_fill_keys([self::SEVERITY_PASS, self::SEVERITY_SKIPPED], true)[$severity]);
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
