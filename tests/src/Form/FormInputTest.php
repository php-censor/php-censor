<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Form;

use PHPCensor\Form\DataTransformer\Yaml;
use PHPCensor\Form\Input;
use PHPUnit\Framework\TestCase;

class FormInputTest extends TestCase
{
    private ?Input $inputElement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inputElement = Input::create('test', 'Test');
    }

    protected function tearDown(): void
    {
        $this->inputElement = null;

        parent::tearDown();
    }

    public function testValidatorSetterGetterSuccess(): void
    {
        $validator = function ($value) {
            echo $value;
        };
        $this->inputElement->setValidator($validator);
        $this->assertEquals($validator, $this->inputElement->getValidator());
    }

    public function testValidatorSetFail(): void
    {
        $this->inputElement->setValidator(5);
        $this->assertNull($this->inputElement->getValidator());
    }

    public function testGetValue(): void
    {
        $this->inputElement->setValue(5);
        $this->assertEquals(5, $this->inputElement->getValue());
    }

    public function testGetValueWithDataTransformer(): void
    {
        $this->inputElement->setDataTransformer(new Yaml());
        $this->inputElement->setValue("key\t=>\tvalue");
        $this->assertEquals('key    =>    value', $this->inputElement->getValue());
    }
}
