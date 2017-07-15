<?php

namespace PHPCensor\Plugin\Util;

/**
 * Class PhpUnitResult parses the results for the PhpUnitV2 plugin
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitResultJson extends PhpUnitResult
{
    const EVENT_TEST        = 'test';
    const EVENT_TEST_START  = 'testStart';
    const EVENT_SUITE_START = 'suiteStart';

    protected $options;
    protected $arguments = [];

    /**
     * Parse the results
     *
     * @return $this
     * @throws \Exception If fails to parse the output
     */
    public function parse()
    {
        $rawResults = file_get_contents($this->outputFile);

        $events = [];
        if ($rawResults && $rawResults[0] == '{') {
            $fixedJson = '[' . str_replace('}{', '},{', $rawResults) . ']';
            $events    = json_decode($fixedJson, true);
        } elseif ($rawResults) {
            $events = json_decode($rawResults, true);
        }

        // Reset the parsing variables
        $this->results  = [];
        $this->errors   = [];
        $this->failures = 0;

        if ($events) {
            foreach ($events as $event) {
                if (isset($event['event']) && $event['event'] == self::EVENT_TEST) {
                    $this->parseTestcase($event);
                }
            }
        }

        return $this;
    }


    /**
     * Build the severity of the event
     *
     * @param $event
     *
     * @return string The severity flags
     * @throws \Exception
     */
    protected function getSeverity($event)
    {
        $status = $event['status'];
        switch ($status) {
            case 'fail':
                $severity = self::SEVERITY_FAIL;
                break;
            case 'error':
                if (strpos($event['message'], 'Skipped') === 0 || strpos($event['message'], 'Incomplete') === 0) {
                    $severity = self::SEVERITY_SKIPPED;
                } else {
                    $severity = self::SEVERITY_ERROR;
                }
                break;
            case 'pass':
                $severity = self::SEVERITY_PASS;
                break;
            case 'warning':
                $severity = self::SEVERITY_PASS;
                break;
            default:
                throw new \Exception("Unexpected PHPUnit test status: {$status}");
                break;
        }

        return $severity;
    }

    /**
     * Build the message string for an event
     *
     * @param array $event
     *
     * @return string
     */
    protected function buildMessage($event)
    {
        $message = $event['test'];

        if ($event['message']) {
            $message .= PHP_EOL . $event ['message'];
        }

        return $message;
    }

    /**
     * Build a string base trace of the failure
     *
     * @param array $event
     *
     * @return string[]
     */
    protected function buildTrace($event)
    {
        $formattedTrace = [];

        if (!empty($event['trace'])) {
            foreach ($event['trace'] as $step){
                $line             = str_replace($this->buildPath, '', $step['file']) . ':' . $step['line'];
                $formattedTrace[] = $line;
            }
        }

        return $formattedTrace;
    }

    /**
     * Saves additional info for a failing test
     *
     * @param array $data
     * @param array $event
     */
    protected function getFileAndLine($event)
    {
        $firstTrace = end($event['trace']);
        reset($event['trace']);

        return [
            'file' => str_replace($this->buildPath, '', $firstTrace['file']),
            'line' => $firstTrace['line']
        ];
    }
}
