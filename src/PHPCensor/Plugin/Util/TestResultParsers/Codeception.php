<?php

namespace PHPCensor\Plugin\Util\TestResultParsers;

use PHPCensor\Builder;

/**
 * Class Codeception
 *
 * @author  Adam Cooper <adam@networkpie.co.uk>
 */
class Codeception implements ParserInterface
{
    protected $builder;
    protected $resultsXml;
    protected $results;
    protected $totalTests;
    protected $totalTimeTaken;
    protected $totalFailures;
    protected $totalErrors;

    /**
     * @param Builder $builder
     * @param $resultsXml
     */
    public function __construct(Builder $builder, $resultsXml)
    {
        $this->builder    = $builder;
        $this->resultsXml = $resultsXml;
        $this->totalTests = 0;
    }

    /**
     * @return array An array of key/value pairs for storage in the plugins result metadata
     */
    public function parse()
    {
        $rtn           = [];
        $this->results = new \SimpleXMLElement($this->resultsXml);

        // calculate total results
        foreach ($this->results->testsuite as $test_suite) {
            $this->totalTests     += (int)$test_suite['tests'];
            $this->totalTimeTaken += (float)$test_suite['time'];
            $this->totalFailures  += (int)$test_suite['failures'];
            $this->totalErrors    += (int)$test_suite['errors'];

            foreach ($test_suite->testcase as $test_case) {
                $test_result = [
                    'suite'      => (string)$test_suite['name'],
                    'file'       => str_replace($this->builder->buildPath, '/', (string) $test_case['file']),
                    'name'       => (string)$test_case['name'],
                    'feature'    => (string)$test_case['feature'],
                    'assertions' => (int)$test_case['assertions'],
                    'time'       => (float)$test_case['time']
                ];

                if (isset($test_case['class'])) {
                    $test_result['class'] = (string) $test_case['class'];
                }

                // PHPUnit testcases does not have feature field. Use class::method instead
                if (!$test_result['feature']) {
                    $test_result['feature'] = sprintf('%s::%s', $test_result['class'], $test_result['name']);
                }

                if (isset($test_case->failure) || isset($test_case->error)) {
                    $test_result['pass']    = false;
                    $test_result['message'] = isset($test_case->failure) ? (string)$test_case->failure : (string)$test_case->error;
                } else {
                    $test_result['pass'] = true;
                }

                $rtn[] = $test_result;
            }
        }

        return $rtn;
    }

    /**
     * Get the total number of tests performed.
     *
     * @return int
     */
    public function getTotalTests()
    {
        return $this->totalTests;
    }

    /**
     * The time take to complete all tests
     *
     * @return mixed
     */
    public function getTotalTimeTaken()
    {
        return $this->totalTimeTaken;
    }

    /**
     * A count of the test failures
     *
     * @return mixed
     */
    public function getTotalFailures()
    {
        return $this->totalFailures + $this->totalErrors;
    }
}
