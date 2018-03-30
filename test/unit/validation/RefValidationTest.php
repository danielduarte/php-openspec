<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\Type\Spec;
use OpenSpec\SpecLibrary;


final class RefValidationTest extends TestCase
{
    protected function getSpecInstance(): Spec
    {
        $refSpecData = [
            'type' => 'ref',
            'spec' => 'Link'
        ];
        $refSpec = SpecBuilder::getInstance()->build($refSpecData);

        $library = SpecLibrary::getInstance();
        if (!$library->hasSpec('Link')) {
            $specData = [
                'type'   => 'object',
                'fields' => [
                    'title' => ['type' => 'string'],
                    'url'   => ['type' => 'string']
                ]
            ];
            $library->registerSpecFromData('Link', $specData);
        }

        return $refSpec;
    }

    protected function getValidValueInstance()
    {
        return [
            'title' => 'Go to Google',
            'url'   => 'http://google.com'
        ];
    }

    protected function getInvalidValueInstance()
    {
        return [
            'title' => null,
            'url'   => 'http://google.com'
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
