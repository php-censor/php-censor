<?php

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\PhpUnitResult;
use PHPCensor\Plugin\Util\PhpUnitResultJson;
use PHPCensor\Plugin\Util\PhpUnitResultJunit;

/**
 * Class PhpUnitResultTest parses the results for the PhpUnitV2 plugin
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitResultTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Skipped test results
     *
     * @var array[]
     */
    static $skipped = [];

    /**
     * @dataProvider getTestData
     */
    public function testInitParse($resultClass, $testFile)
    {
        $buildPath = '/path/to/build';
        $parser = new $resultClass(ROOT_DIR . $testFile, $buildPath);
        $output = $parser->parse()->getResults();
        $errors = $parser->getErrors();

        self::assertEquals(7, $parser->getFailures());
        self::assertInternalType('array', $output);
        self::assertInternalType('array', $errors);
        self::assertNotEmpty($output);
        self::assertNotEmpty($errors);

        // The trace elements should not include the build path
        self::assertStringStartsNotWith($buildPath, $output[3]['trace'][0]);
        self::assertStringStartsNotWith($buildPath, $output[3]['trace'][1]);

        self::assertEquals("some output\nfrom f4", $output[7]['output']);
        self::assertEquals("has output\non lines", $output[15]['output']);

        self::assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[5]['severity']);
        try {
            self::assertContains('Incomplete Test:', $output[5]['message']);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        }

        self::assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[11]['severity']);
        try {
            self::assertContains('Skipped Test:', $output[11]['message']);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        }
    }

    /**
     * used as long as junit format does not provide message for skipped tests
     */
    public function testSkippedAnything()
    {
        if (self::$skipped) {
            $msg = "Skipped result tests:\n";
            foreach (self::$skipped as $skip) {
                $exMsg = strstr((string)$skip['ex'], "\n", true);
                if (false === $exMsg) {
                    $exMsg = (string)$skip['ex'];
                }
                $msg .= sprintf(" * %s: %s \n", $skip['cls'], $exMsg);
            }
            $this->markTestSkipped($msg);
        }
    }

    public static function getTestData()
    {
        return [
            'json' => [PhpUnitResultJson::class, 'tests/PHPCensor/Plugin/SampleFiles/phpunit_money.txt'],
            'junit' => [PhpUnitResultJunit::class, 'tests/PHPCensor/Plugin/SampleFiles/phpunit_money_junit.xml'],
        ];
    }
}
