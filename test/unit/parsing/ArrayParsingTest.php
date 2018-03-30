<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\ArraySpec;
use OpenSpec\Spec\Type\Spec;
use OpenSpec\ParseSpecException;


final class ArrayParsingTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = [
            'type'  => 'array',
            'items' => ['type' => 'string']
        ];

        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    public function testParseSpecResult()
    {
        $spec = $this->getSpecInstance();

        $this->assertInstanceOf(ArraySpec::class, $spec);
    }

    public function testSpecCorrectTypeName()
    {
        $spec = $this->getSpecInstance();

        $this->assertEquals($spec->getTypeName(), 'array');
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
        $this->assertEquals($fields, ['items']);
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
            'type'                        => 'array',
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
        $specData = ['type' => 'array', 'items' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testValidArrayOfAny()
    {
        $specData = ['type' => 'array'];

        try {
            SpecBuilder::getInstance()->build($specData);
            $errors = [];
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        $this->assertTrue(count($errors) === 0, "Spec for array of any element not validates as expected.");
    }
}
