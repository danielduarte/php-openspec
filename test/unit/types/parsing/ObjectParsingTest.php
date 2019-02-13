<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\ObjectSpec;
use OpenSpec\Spec\Type\TypeSpec;
use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


final class ObjectParsingTest extends TestCase
{
    protected function getSpecInstance(): TypeSpec
    {
        $specData = [
            'type'       => 'object',
            'fields'     => [
                'field1' => ['type' => 'string'],
                'field2' => ['type' => 'string'],
            ],
            'requiredFields' => ['field1'],
            'extensible' => true
        ];
        $spec = SpecBuilder::getInstance()->build($specData, new SpecLibrary());

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
        $this->assertEquals($fields, ['extensible', 'extensionFieldNamesPattern', 'extensionFields', 'fields', 'requiredFields']);
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
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_UNEXPECTED_FIELDS));
    }

    public function testFieldFieldsOfInvalidType()
    {
        $specData = ['type' => 'object', 'fields' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testFieldRequiredFieldsOfInvalidType()
    {
        $specData = ['type' => 'object', 'fields' => [], 'requiredFields' => 'An array should be here'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testFieldRequiredFieldsWithInvalidItems()
    {
        $specData = ['type' => 'object', 'fields' => [], 'requiredFields' => ['correct1', 'correct2', 1234, 'correct3']];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_STRING_EXPECTED));
    }

    public function testFieldExtensibleOfInvalidType()
    {
        $specData = ['type' => 'object', 'extensible' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_BOOLEAN_EXPECTED));
    }

    public function testFieldExtensionFieldsOfInvalidType()
    {
        $specData = ['type' => 'object', 'extensible' => true, 'extensionFields' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }

    public function testExtensionFieldsWithoutExtensible()
    {
        $specData = ['type' => 'object', 'extensionFields' => ['type' => 'string']];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_MISSING_NEEDED_FIELD));
    }

    public function testExtensionFieldsWithExtensibleFalse()
    {
        $specData = ['type' => 'object', 'extensible' => false, 'extensionFields' => ['type' => 'string']];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_EXTENSIBLE_EXPECTED));
    }

    public function testValidObjectWithNoFieldSpecs()
    {
        $specData = ['type' => 'object'];

        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
            $errors = [];
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        $this->assertTrue(count($errors) === 0, "Spec for object with no field specs not validates as expected.");
    }

    public function testExtensionFieldsWithValidPattern()
    {
        // @todo Create test for invalid regexs in 'extensionFieldNamesPattern'
        $specData = [
            'type' => 'object',
            'extensible' => true,
            'extensionFieldNamesPattern' => '^x-'
        ];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
            $errors = [];
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        $this->assertTrue(count($errors) === 0, "Spec for object with extension field names with pattern not validates as expected:" . PHP_EOL . '- ' . implode(PHP_EOL . '- ', array_column($errors, 1)));
    }

    public function testExtensionFieldsWithInvalidPatternType()
    {
        $specData = [
            'type' => 'object',
            'extensible' => true,
            'extensionFieldNamesPattern' => 12345
        ];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_STRING_EXPECTED), "Expected invalid regular expression type for 'extensionFieldNamesPattern'.");
    }

    public function testExtensionFieldsWithInvalidRegexPattern()
    {
        $specData = [
            'type' => 'object',
            'extensible' => true,
            'extensionFieldNamesPattern' => '/'
        ];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData, new SpecLibrary());
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_INVALID_REGEX_FOR_EXTENSIBLE_FIELDNAMES), "Expected invalid regular expression for 'extensionFieldNamesPattern'.");
    }
}
