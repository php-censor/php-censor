<?php

namespace PHPCensor\Plugin\Util;

use Exception;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * Class PhpUnitResult parses the results for the PhpUnitV2 plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class PhpUnitResultJson extends PhpUnitResult
{
    public const EVENT_TEST        = 'test';
    public const EVENT_TEST_START  = 'testStart';
    public const EVENT_SUITE_START = 'suiteStart';

    protected $options;
    protected $arguments = [];

    /**
     * Parse the results
     *
     * @return $this
     * @throws Exception If fails to parse the output
     */
    public function parse()
    {
        $rawResults = \file_get_contents($this->outputFile);

        $events = [];
        if ($rawResults && $rawResults[0] === '{') {
            $fixedJson = '[' . \str_replace('}{', '},{', $rawResults) . ']';
            $events    = \json_decode($fixedJson, true);
        } elseif ($rawResults) {
            $events = \json_decode($rawResults, true);
        }

        // Reset the parsing variables
        $this->results  = [];
        $this->errors   = [];
        $this->failures = 0;

        if ($events) {
            $started = null;
            foreach ($events as $event) {
                if (isset($event['event']) && $event['event'] === self::EVENT_TEST) {
                    $this->parseTestcase($event);
                    $started = null;
                } elseif (isset($event['event']) && $event['event'] === self::EVENT_TEST_START) {
                    $started = $event;
                }
            }
            if ($started) {
                $event = $started;
                $event['status'] = 'error';
                $event['message'] = 'Test is not finished';
                $event['output'] = '';
                $this->parseTestcase($event);
            }
        }

        return $this;
    }


    /**
     * Build the severity of the event
     *
     *
     * @return string The severity flags
     * @throws Exception
     */
    protected function getSeverity($testCase)
    {
        $status = $testCase['status'];
        switch ($status) {
            case 'fail':
                $severity = self::SEVERITY_FAIL;

                break;
            case 'error':
                if (\strpos($testCase['message'], 'Skipped') === 0 || \strpos($testCase['message'], 'Incomplete') === 0) {
                    $severity = self::SEVERITY_SKIPPED;
                } else {
                    $severity = self::SEVERITY_ERROR;
                }

                break;
            case 'pass':
            case 'warning':
                $severity = self::SEVERITY_PASS;

                break;
            default:
                throw new RuntimeException("Unexpected PHPUnit test status: {$status}");
        }

        return $severity;
    }

    /**
     * Build the message string for an event
     *
     * @param array $testCase
     *
     * @return string
     */
    protected function buildMessage($testCase)
    {
        $message = $testCase['test'];

        if ($testCase['message']) {
            $message .= PHP_EOL . $testCase ['message'];
        }

        return $message;
    }

    /**
     * Build a string base trace of the failure
     *
     * @param array $testCase
     *
     * @return string[]
     */
    protected function buildTrace($testCase)
    {
        $formattedTrace = [];

        if (!empty($testCase['trace'])) {
            foreach ($testCase['trace'] as $step) {
                $line             = \str_replace($this->buildPath, '', $step['file']) . ':' . $step['line'];
                $formattedTrace[] = $line;
            }
        }

        return $formattedTrace;
    }

    /**
     * Saves additional info for a failing test
     *
     * @param array $testCase
     *
     * @return array
     */
    protected function getFileAndLine($testCase)
    {
        if (empty($testCase['trace'])) {
            return [
                'file' => '',
                'line' => '',
            ];
        }
        $firstTrace = \end($testCase['trace']);
        \reset($testCase['trace']);

        return [
            'file' => \str_replace($this->buildPath, '', $firstTrace['file']),
            'line' => $firstTrace['line']
        ];
    }
}
