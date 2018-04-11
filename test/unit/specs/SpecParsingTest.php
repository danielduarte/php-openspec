<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\ParseSpecException;
use OpenSpec\Spec\OpenSpec;


final class SpecParsingTest extends TestCase
{
    public function testParseValidSpec()
    {
        $specData = [
            'openspec' => '1.0.6',
            'name'     => 'The Name',
            'version'  => '1.0.0',
            'spec'     => ['type' => 'string']
        ];

        try {
            new OpenSpec($specData);
            $errorMsg = null;
        } catch (ParseSpecException $ex) {
            $errorMsg = $ex->getMessage();
        }

        $this->assertTrue($errorMsg === null, 'Error trying to parse valid spec:' . PHP_EOL . $errorMsg);
    }

    public function testParseNotValidSpec()
    {
        $specData = [
            'name'     => 'The Name',
            'version'  => '1.0.0',
            'spec'     => ['type' => 'string']
        ];

        $exception = null;
        try {
            new OpenSpec($specData);
        } catch (ParseSpecException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception !== null && $exception->containsError(ParseSpecException::CODE_MISSING_REQUIRED_FIELD), "Expected 'missing required field' error trying to parse invalid spec, but no error occurred.");
    }
}
