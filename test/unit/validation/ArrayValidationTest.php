<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;
use OpenSpec\Spec\StringSpec;
use OpenSpec\Spec\Spec;
use OpenSpec\ParseSpecException;


final class ArrayValidationTest extends TestCase
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

    protected function getValidValueInstance()
    {
        return ['one', 'two', 'three'];
    }

    protected function getInvalidValueInstance()
    {
        return [1, 2, 3];
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

    public function testSeveralValidations()
    {
        // 1 ------------------------------------------------
        $specData  = [
            'type'  => 'array',
            'items' => [
                'type' => 'array',
                'items' => [
                    'type' => 'mixed',
                    'options' => [
                        ['type' => 'string'],
                        ['type' => 'boolean']
                    ]
                ]
            ]
        ];

        $value = [
            [true, false, true],
            ['string', 'value', false],
            [],
            ['other', 'another one']
        ];

        $spec = SpecBuilder::getInstance()->build($specData);
        $result = $spec->validate($value);

        $this->assertTrue($result, "Not validated array of arrays of string|boolean.");
        // End: 1 ------------------------------------------------

        // 2 ------------------------------------------------
        $specData  = [
            'type'  => 'object',
            'extensible' => true,
            'fields' => [
                'name' => ['type' => 'string'],
                'happy' => ['type' => 'boolean'],
                'age' => ['type' => 'string'],
                'hobbies' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string'
                    ]
                ],
                'address' => [
                    'type' => 'object',
                    'fields' => [
                        'country' => ['type' => 'string'],
                        'city'    => ['type' => 'string'],
                        'phones'  => ['type' => 'array', 'items' => ['type' => 'string']],
                    ]
                ],
            ]
        ];

        $value = [
            'name'    => 'Daniel',
            'happy'   => true,
            'age'     => '37',
            'hobbies' => ['numismatics', 'rubik cubes'],
            'address' => [
                'country' => 'Argentina',
                'city'    => 'Tandil',
                'phones'  => []
            ],
            'comments' => ''
        ];

        $spec = SpecBuilder::getInstance()->build($specData);
        $result = $spec->validate($value);

        $this->assertTrue($result, "Not validated person info.");
        // End: 2 ------------------------------------------------

        // 3 ------------------------------------------------
        $specData  = ['type'  => 'array'];
        $values = [
            [], // Empty array
            [false, true, true], // Boolean array
            ["hello", "bye"], // String array
            [null, null, null], // Null array
            [['name' => 'Daniel', 'alias' => 'Dani'], ['a' => 1, 'b' => [2, 3]]], // Object array
            [[], ['field' => 'value'], [null, false, "hi!"]], // Array of arrays (bidimensional array / matrix)
        ];

        $spec = SpecBuilder::getInstance()->build($specData);
        foreach ($values as $value) {
            $result = $spec->validate($value);
            $this->assertTrue($result, "Not validated array.");
        }

        $complexValue = $values;
        $result = $spec->validate($complexValue);
        $this->assertTrue($result, "Not validated array of any type of items.");
        // End: 3 ------------------------------------------------
    }
}
