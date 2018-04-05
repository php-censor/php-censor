<?php

namespace PHPCensor\Plugin\Util;

/**
 * Class PhpUnitResultJunit parses the results for the PhpUnitV2 plugin
 *
 * @author Simon Heimberg <simon.heimberg@heimberg-ea.ch>
 */
class PhpUnitResultJunit extends PhpUnitResult
{
    /**
     * Parse the results
     *
     * @return $this
     * @throws \Exception If fails to parse the output
     */
    public function parse()
    {
        $suites = simplexml_load_file($this->outputFile);

        // Reset the parsing variables
        $this->results  = [];
        $this->errors   = [];
        $this->failures = 0;

        foreach ($suites->xpath('//testcase') as $testCase) {
            $this->parseTestcase($testCase);
        }
        $suites['failures'];
        $suites['errors'];

        return $this;
    }

    protected function getSeverity($testCase)
    {
        $severity = self::SEVERITY_PASS;
        foreach($testCase as $child) {
            switch ($child->getName()) {
                case 'failure':
                    $severity = self::SEVERITY_FAIL;
                    break 2;
                case 'error':
                    if ('PHPUnit\Framework\RiskyTestError' == $child['type']) { // == because convertion to string is desired
                        $severity = self::SEVERITY_RISKY;
                    } else {
                        $severity = self::SEVERITY_ERROR;
                    }
                    break 2;
                case 'skipped':
                    // skipped and ignored, can not distinguish
                    $severity = self::SEVERITY_SKIPPED;
                    break 2;
                case 'warning':
                    $severity = self::SEVERITY_WARN;
                    break 2;
                case 'system-out':
                case 'system-err':
                    // not results
                    continue;
                default:
                    $severity = 'UNKNOWN RESULT TYPE: '.$child->getName();
                    break 2;
            }
        }

        return $severity;
   }

    protected function buildMessage($testCase)
    {
        $tracePos = -1;
        $msg = $this->getMessageTrace($testCase);
        if ('' !== $msg) {
            //strip trace
            $trPos = strrpos($msg, "\n\n");
            if (false !== $trPos) {
                $tracePos = $trPos;
                $msg = substr($msg, 0, $trPos);
            }
        }
        if ('' === $msg) {
            $msg = $testCase['class'].'::'.$testCase['name'];
        };
        $testCase['_tracePos'] = $tracePos; // will be converted to string

        return $msg;
    }

    protected function getOutput($testCase) {
        return (string)$testCase->{'system-out'};
    }

    protected function buildTrace($testCase)
    {
        if (!is_int($testCase['_tracePos'])) {
            $this->buildMessage($testCase);
        }

        if ($testCase['_tracePos'] >= 0) {
            $stackStr = substr($this->getMessageTrace($testCase), (int)$testCase['_tracePos'] + 2, -1);
            $trace = explode("\n", str_replace($this->buildPath, '.', $stackStr));
        } else {
            $trace = array();
        }

        return $trace;
    }

    private function getMessageTrace($testCase) {
        $msg = '';
        foreach($testCase as $child) {
            switch ($child->getName()) {
                case 'system-out':
                case 'system-err':
                    // not results
                    continue;
                default:
                    $msg = (string)$child['message']; // according to xsd
                    if ('' === $msg) {
                        $msg = (string)$child;
                    }
                    break 2;
            }
        }

        return $msg;
    }
}
