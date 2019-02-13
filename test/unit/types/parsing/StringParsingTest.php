<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\StringSpec;
use OpenSpec\Spec\Type\TypeSpec;
use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


final class StringParsingTest extends TestCase
{
    protected function getSpecInstance(): TypeSpec
    {
        $specData = ['type' => 'string'];
        $spec = SpecBuilder::getInstance()->build($specData, new SpecLibrary());

        return $spec;
    }

    public function testParseSpecResult()
    {
        $spec = $this->getSpecInstance();

        $this->assertInstanceOf(StringSpec::class, $spec);
    }

    public function testSpecCorrectTypeName()
    {
        $spec = $this->getSpecInstance();

        $this->assertEquals($spec->getTypeName(), 'string');
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

    public function testUnexpectedFields()
    {
        $specData = [
            'type'                        => 'string',
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
}
