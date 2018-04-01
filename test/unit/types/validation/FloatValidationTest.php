<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\FloatSpec;
use OpenSpec\Spec\Type\TypeSpec;
use OpenSpec\ParseSpecException;


final class FloatValidationTest extends TestCase
{
    protected function getSpecInstance(): TypeSpec
    {
        $specData = ['type' => 'float'];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    protected function getValidValueInstance()
    {
        return 3.14159265359;
    }

    protected function getInvalidValueInstance()
    {
        return 3;
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
