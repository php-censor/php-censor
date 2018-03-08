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
        foreach ($this->results->testsuite as $testSuite) {
            $this->totalTests     += (int)$testSuite['tests'];
            $this->totalTimeTaken += (float)$testSuite['time'];
            $this->totalFailures  += (int)$testSuite['failures'];
            $this->totalErrors    += (int)$testSuite['errors'];

            foreach ($testSuite->testcase as $testCase) {
                $testResult = [
                    'suite'      => (string)$testSuite['name'],
                    'file'       => str_replace($this->builder->buildPath, '/', (string) $testCase['file']),
                    'name'       => (string)$testCase['name'],
                    'feature'    => (string)$testCase['feature'],
                    'assertions' => (int)$testCase['assertions'],
                    'time'       => (float)$testCase['time']
                ];

                if (isset($testCase['class'])) {
                    $testResult['class'] = (string) $testCase['class'];
                }

                // PHPUnit testcases does not have feature field. Use class::method instead
                if (!$testResult['feature']) {
                    $testResult['feature'] = sprintf('%s::%s', $testResult['class'], $testResult['name']);
                }

                if (isset($testCase->failure) || isset($testCase->error)) {
                    $testResult['pass']    = false;
                    $testResult['message'] = isset($testCase->failure) ? (string)$testCase->failure : (string)$testCase->error;
                } else {
                    $testResult['pass'] = true;
                }

                $rtn[] = $testResult;
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
