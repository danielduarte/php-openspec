<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\MixedSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class MixedParsingTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $specData = [
            'type' => 'mixed',
            'options' => []
        ];
        $spec = SpecBuilder::getInstance()->build($specData);

        return $spec;
    }

    public function testParseSpecResult()
    {
        $spec = $this->getSpecInstance();

        $this->assertInstanceOf(MixedSpec::class, $spec);
    }

    public function testSpecCorrectTypeName()
    {
        $spec = $this->getSpecInstance();

        $this->assertEquals($spec->getTypeName(), 'mixed');
    }

    public function testSpecRequiredFields()
    {
        $spec = $this->getSpecInstance();

        $fields = $spec->getRequiredFields();
        sort($fields);
        $this->assertEquals($fields, ['options', 'type']);
    }

    public function testSpecOptionalFields()
    {
        $spec = $this->getSpecInstance();

        $fields = $spec->getOptionalFields();
        sort($fields);
        $this->assertEquals($fields, []);
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

    public function testMissingRequiredFields()
    {
        $specData = ['type' => 'mixed'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_MISSING_REQUIRED_FIELD));
    }

    public function testUnexpectedFields()
    {
        $specData = [
            'type'                        => 'mixed',
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
        $specData = ['type' => 'mixed', 'options' => 'this is a string'];

        $exception = null;
        try {
            SpecBuilder::getInstance()->build($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception->containsError(ParseSpecException::CODE_ARRAY_EXPECTED));
    }
}
