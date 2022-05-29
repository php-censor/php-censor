<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Form\DataTransformer;

use PHPCensor\Form\DataTransformer\Yaml;
use PHPUnit\Framework\TestCase;

class YamlDataTransformerTest extends TestCase
{
    private string $textToTransform = 'some_unchanged_text transformed to be displayed in forms';
    private string $textForReverseTransform = "some_text_with_tabs\t and \tanother\tone";
    private string $textAfterReverseTransform = "some_text_with_tabs\t and \tanother\tone";

    public function testTransform()
    {
        $transformer = new Yaml();
        $this->assertEquals($this->textToTransform, $transformer->transform($this->textToTransform));
        $this->assertEquals($this->textForReverseTransform, $transformer->transform($this->textForReverseTransform));
    }

    public function testReverveTransform()
    {
        $transformer = new Yaml();
        $this->assertEquals($this->textToTransform, $transformer->reverseTransform($this->textToTransform));
        $this->assertEquals($this->textAfterReverseTransform, $transformer->transform($this->textForReverseTransform));
    }
}
