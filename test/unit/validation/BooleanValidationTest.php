<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\StringSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class BooleanValidationTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = ['type' => 'boolean'];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    protected function getValidValueInstance()
    {
        return false;
    }

    protected function getInvalidValueInstance()
    {
        return 'a string, invalid boolean value';
    }

    public function testValidValue()
    {
        $spec  = $this->getSpecInstance();
        $value = $this->getValidValueInstance();

        $result = $spec->validate($value);

        $this->assertTrue($result, "Given value not recognized by the spec, even when it should.");
    }

    public function testInvalidValue()
    {
        $spec  = $this->getSpecInstance();
        $value = $this->getInvalidValueInstance();

        $result = $spec->validate($value);

        $this->assertTrue(!$result, "Given value recognized by the spec, even when it should not.");
    }
}
