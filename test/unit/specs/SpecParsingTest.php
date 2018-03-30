<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


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
            new \OpenSpec\Spec\Spec($specData);
            $error = false;
        } catch (\Exception $ex) {
            $error = true;
        }

        $this->assertFalse($error, 'Error trying to parse valid spec.');
    }

    public function testParseNotValidSpec()
    {
        $specData = [
            'name'     => 'The Name',
            'version'  => '1.0.0',
            'spec'     => ['type' => 'string']
        ];

        try {
            new \OpenSpec\Spec\Spec($specData);
            $error = false;
        } catch (\Exception $ex) {
            $error = true;
        }

        $this->assertTrue($error, 'Expected error trying to parse invalid spec, but no error occurred.');
    }
}
