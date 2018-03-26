<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\StringSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class ObjectValidationTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = [
            'type'  => 'object',
            'fields' => [
                'name'  => ['type' => 'string'],
                'happy' => ['type' => 'boolean'],
            ]
        ];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    protected function getValidValueInstance()
    {
        return [
            'name'  => 'Daniel',
            'happy' => true
        ];
    }

    protected function getInvalidValueInstance()
    {
        return [
            'name'  => 'Daniel',
            'happy' => 'yes'
        ];
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
