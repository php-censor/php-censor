<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\PhpUnitResult;
use PHPCensor\Plugin\Util\PhpUnitResultJson;
use PHPCensor\Plugin\Util\PhpUnitResultJunit;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpUnitResultTest parses the results for the PhpUnitV2 plugin
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitResultTest extends TestCase
{
    /**
     * Skipped test results
     */
    public static array $skipped = [];

    /**
     * @dataProvider getTestData
     */
    public function testInitParse(string $resultClass, string $testFile): void
    {
        $buildPath = '/path/to/build';
        $parser = new $resultClass(ROOT_DIR . $testFile, $buildPath);
        $output = $parser->parse()->getResults();
        $errors = $parser->getErrors();

        self::assertEquals(7, $parser->getFailures());
        self::assertIsArray($output);
        self::assertIsArray($errors);
        self::assertNotEmpty($output);
        self::assertNotEmpty($errors);

        // The trace elements should not include the build path
        self::assertStringStartsNotWith($buildPath, $output[3]['trace'][0]);
        self::assertStringStartsNotWith($buildPath, $output[3]['trace'][1]);

        self::assertEquals("some output\nfrom f4", $output[7]['output']);
        self::assertEquals("has output\non lines", $output[15]['output']);

        self::assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[5]['severity']);

        try {
            self::assertStringContainsString('Incomplete Test:', $output[5]['message']);
        } catch (ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        }

        self::assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[11]['severity']);

        try {
            self::assertStringContainsString('Skipped Test:', $output[11]['message']);
        } catch (ExpectationFailedException $e) {
            self::$skipped[] = ['cls' => $resultClass, 'ex' => $e];
        }
    }

    public static function getTestData(): array
    {
        return [
            'json'  => [PhpUnitResultJson::class, 'tests/data/Plugin/PhpUnit/phpunit_money.txt'],
            'junit' => [PhpUnitResultJunit::class, 'tests/data/Plugin/PhpUnit/phpunit_money_junit.xml'],
        ];
    }
}
