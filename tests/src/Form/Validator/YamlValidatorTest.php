<?php

namespace Tests\PHPCensor\Form\Validator;

use PHPCensor\Form\Validator\Yaml;
use PHPUnit\Framework\TestCase;

class YamlValidatorTest extends TestCase
{
    public function getDatasetSuccess()
    {
        return [
            ["php-censor:\n    language: en\n    per_page: 10\n\n"],
            ["build_settings:\n  clone_depth: 1\n\nsetup:\n    composer:\n        action: \"install\"\n        directory: \"core/libraries\""],
            ["{  }"]
        ];
    }

    public function getDatasetFail()
    {
        return [
            ["php-censor:\n    language: en\n   per_page: 10\n\n"],
            ["php-censor:\n\tlanguage: en\n    per_page: 10\n\n"],
            ["php-censor:\n\tlanguage: en\n\tper_page: 10\n\n"],
        ];
    }

    /**
     * @dataProvider getDatasetSuccess
     */
    public function testYamlValidatorSuccess($value)
    {
        $validator = new Yaml();
        $result = call_user_func_array($validator, [$value]);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getDatasetFail
     */
    public function testYamlValidatorFail($value)
    {
        $this->expectException('Exception');
        $validator = new Yaml();
        call_user_func_array($validator, [$value]);
    }
}
