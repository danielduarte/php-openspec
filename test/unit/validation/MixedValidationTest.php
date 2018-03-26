<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\StringSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class MixedValidationTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = [
            'type' => 'mixed',
            'options' => [
                ['type' => 'string'],
                ['type' => 'boolean']
            ]
        ];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    protected function getValidValueInstance()
    {
        return 'a string';
    }

    protected function getInvalidValueInstance()
    {
        return [false, 'a string', 12345, true, false, 'another string'];
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
