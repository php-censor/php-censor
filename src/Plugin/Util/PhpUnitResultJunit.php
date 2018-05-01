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
        // Reset the parsing variables
        $this->results  = [];
        $this->errors   = [];
        $this->failures = 0;

        $suites = $this->loadResultFile();

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

    /**
     * @return \SimpleXMLElement
     */
    private function loadResultFile()
    {
        if (!file_exists($this->outputFile) || 0 === filesize($this->outputFile)) {
            $this->internalProblem('empty output file');

            return new \SimpleXMLElement('<empty/>'); // new empty element
        }

        try {
            $suites = simplexml_load_file($this->outputFile);
        } catch (\Exception $ex) {
            $suites = null;
        } catch (\Throwable $ex) { // since php7
            $suites = null;
        }
        if (!$suites) {
            // from https://stackoverflow.com/questions/7766455/how-to-handle-invalid-unicode-with-simplexml/8092672#8092672
            $oldUse = libxml_use_internal_errors(true);
            libxml_clear_errors();
            $dom = new \DOMDocument("1.0", "UTF-8");
            $dom->strictErrorChecking = false;
            $dom->validateOnParse = false;
            $dom->recover = true;
            $dom->loadXML(strtr(
                file_get_contents($this->outputFile),
                array('&quot;' => "'") // &quot; in attribute names may mislead the parser
            ));

            /**
             * @var \LibXMLError
             */
            $xmlError = libxml_get_last_error();
            if ($xmlError) {
                $warning = sprintf('L%s C%s: %s', $xmlError->line, $xmlError->column, $xmlError->message);
                print 'WARNING: ignored errors while reading phpunit result, '.$warning."\n";
            }
            if (!$dom->hasChildNodes()) {
                $this->internalProblem('xml file with no content');
            }
            $suites = simplexml_import_dom($dom);

            libxml_clear_errors();
            libxml_use_internal_errors($oldUse);
        }

        return $suites;
    }

    /**
     * @param string $description
     */
    private function internalProblem($description)
    {
        throw new \RuntimeException($description);

        // alternative to error throwing: append to $this->errors
    }
}
