<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\TypeSpec;
use OpenSpec\SpecLibrary;


final class IntegerValidationTest extends TestCase
{
    protected function getSpecInstance(): TypeSpec
    {
        $specData = ['type' => 'integer'];
        $spec = SpecBuilder::getInstance()->build($specData, new SpecLibrary());

        return $spec;
    }

    protected function getValidValueInstance()
    {
        return 456;
    }

    protected function getInvalidValueInstance()
    {
        return 'this is not an int';
    }

    // @todo modify similar tests to use validateGetErrors instead of validate
    public function testValidValue()
    {
        $spec  = $this->getSpecInstance();
        $value = $this->getValidValueInstance();

        $errors = $spec->validateGetErrors($value);

        $msg = '- ' . implode(PHP_EOL . '- ', array_column($errors, 1));
        $this->assertTrue(count($errors) === 0, "Given value not recognized by the spec, even when it should." . PHP_EOL . $msg);
    }

    public function testInvalidValue()
    {
        $spec  = $this->getSpecInstance();
        $value = $this->getInvalidValueInstance();

        $result = $spec->validate($value);

        $this->assertTrue(!$result, "Given value recognized by the spec, even when it should not.");
    }
}
