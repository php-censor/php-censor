<?php

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\PhpUnitResult;
use PHPCensor\Plugin\Util\PhpUnitResultJson;

/**
 * Class PhpUnitResultTest parses the results for the PhpUnitV2 plugin
 * @author       Pablo Tejada <pablo@ptejada.com>
 * @package      PHPCI
 * @subpackage   Plugin
 */
class PhpUnitResultTest extends \PHPUnit_Framework_TestCase
{

    public function testInitParse()
    {
        $buildPath = '/path/to/build';
        $parser = new PhpUnitResultJson(ROOT_DIR . 'tests/PHPCensor/Plugin/SampleFiles/phpunit_money.txt', $buildPath);
        $output = $parser->parse()->getResults();
        $errors = $parser->getErrors();

        $this->assertEquals(7, $parser->getFailures());
        $this->assertInternalType('array', $output);
        $this->assertInternalType('array', $errors);
        $this->assertNotEmpty($output);
        $this->assertNotEmpty($errors);

        // The trace elements should not include the build path
        $this->assertStringStartsNotWith($buildPath, $output[3]['trace'][0]);
        $this->assertStringStartsNotWith($buildPath, $output[3]['trace'][1]);

        $this->assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[5]['severity']);
        $this->assertContains('Incomplete Test:', $output[5]['message']);

        $this->assertEquals(PhpUnitResult::SEVERITY_SKIPPED, $output[11]['severity']);
        $this->assertContains('Skipped Test:', $output[11]['message']);
    }
}
