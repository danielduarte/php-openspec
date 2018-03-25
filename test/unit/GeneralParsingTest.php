<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


final class GeneralParsingTest extends TestCase
{
    public function testParseInvalidDataSpecError()
    {
        $this->expectException(ParseSpecException::class);
        $this->expectExceptionCode(ParseSpecException::CODE_ARRAY_EXPECTED);

        $specData = 123456;

        SpecBuilder::getInstance()->build($specData);
    }

    public function testParseInvalidTypeOfTypeSpecError()
    {
        $this->expectException(ParseSpecException::class);
        $this->expectExceptionCode(ParseSpecException::CODE_INVALID_TYPE_NAME_TYPE);

        $specData = ['type' => true];

        SpecBuilder::getInstance()->build($specData);
    }

    public function testParseUnknownTypeSpecError()
    {
        $this->expectException(ParseSpecException::class);
        $this->expectExceptionCode(ParseSpecException::CODE_UNKNOWN_SPEC_TYPE);

        $specData = ['type' => 'any_weird_string'];

        SpecBuilder::getInstance()->build($specData);
    }
}
