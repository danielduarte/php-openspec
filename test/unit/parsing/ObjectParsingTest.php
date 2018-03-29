<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\ObjectSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class ObjectParsingTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = [
            'type'       => 'object',
            'fields'     => [],
            'extensible' => true
        ];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    public function testParseSpecResult()
    {
        $spec = $this->getSpecInstance();

        $this->assertInstanceOf(ObjectSpec::class, $spec);
    }

    public function testSpecCorrectTypeName()
    {
        $spec = $this->getSpecInstance();

        $this->assertEquals($spec->getTypeName(), 'object');
    }

    public function testSpecRequiredFields()
    {
        $spec = $this->getSpecInstance();

        $fields = $spec->getRequiredFields();
        sort($fields);
        $this->assertEquals($fields, ['type']);
    }

    public function testSpecOptionalFields()
    {
        $spec = $this->getSpecInstance();

        $fields = $spec->getOptionalFields();
        sort($fields);
        $this->assertEquals($fields, ['extensible', 'extensionFields', 'fields']);
    }

    public function testSpecAllFields()
    {
        $spec = $this->getSpecInstance();

        $reqFields = $spec->getRequiredFields();
        $optFields = $spec->getOptionalFields();
        $allFieldsCalculated = array_unique(array_merge($reqFields, $optFields));
        sort($allFieldsCalculated);

        $allFields = $spec->getAllFields();
        sort($allFields);

        $this->assertEquals($allFieldsCalculated, $allFields);
    }

    public function testUnexpectedFields()
    {
        $specData = [
            'type'                        => 'object',
            'this_is_an_unexpected_field' => 1234,
            'and_this_is_other'           => ['a', 'b']
        ];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_UNEXPECTED_FIELDS));
    }

    public function testFieldItemsOfInvalidType()
    {
        $specData = ['type' => 'object', 'fields' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testFieldExtensibleOfInvalidType()
    {
        $specData = ['type' => 'object', 'extensible' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_INVALID_SPEC_DATA));
    }

    public function testFieldExtensionFieldsOfInvalidType()
    {
        $specData = ['type' => 'object', 'extensible' => true, 'extensionFields' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testExtensionFieldsWithoutExtensible()
    {
        $specData = ['type' => 'object', 'extensionFields' => ['type' => 'string']];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_EXTENSIBLE_EXPECTED));
    }

    public function testExtensionFieldsWithExtensibleFalse()
    {
        $specData = ['type' => 'object', 'extensible' => false, 'extensionFields' => ['type' => 'string']];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_EXTENSIBLE_EXPECTED));
    }

    public function testValidObjectWithNoFieldSpecs()
    {
        $specData = ['type' => 'object'];

        try {
            SpecBuilder::getInstance()->build($specData);
            $errors = [];
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        $this->assertTrue(count($errors) === 0, "Spec for object with no field specs not validates as expected.");
    }
}
