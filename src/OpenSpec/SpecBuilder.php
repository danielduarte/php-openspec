<?php

namespace OpenSpec;

use OpenSpec\Spec\Type\TypeSpec;


class SpecBuilder
{
    private static $_instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SpecBuilder
    {
        if (static::$_instance === null) {
            static::$_instance = new SpecBuilder();
        }

        return static::$_instance;
    }

    public function build($specData): TypeSpec
    {
        if (!is_array($specData)) {
            throw new ParseSpecException('Expected array as spec data, but ' . gettype($specData) . ' given.', ParseSpecException::CODE_ARRAY_EXPECTED);
        }

        if (!array_key_exists('type', $specData)) {
            throw new ParseSpecException("Field 'type' not specified in spec data.", ParseSpecException::CODE_MISSING_REQUIRED_FIELD);
        }

        $type = $specData['type'];
        if (!is_string($type)) {
            throw new ParseSpecException("Expected 'type' of spec to be a string value.", ParseSpecException::CODE_INVALID_TYPE_NAME_TYPE);
        }

        $classMap = [
            'null'    => '\OpenSpec\Spec\Type\NullSpec',
            'boolean' => '\OpenSpec\Spec\Type\BooleanSpec',
            'string'  => '\OpenSpec\Spec\Type\StringSpec',
            'object'  => '\OpenSpec\Spec\Type\ObjectSpec',
            'array'   => '\OpenSpec\Spec\Type\ArraySpec',
            'mixed'   => '\OpenSpec\Spec\Type\MixedSpec',
            'ref'     => '\OpenSpec\Spec\Type\RefSpec',
        ];

        if (!array_key_exists($type, $classMap)) {
            throw new ParseSpecException("Unknown spec type '$type'.", ParseSpecException::CODE_UNKNOWN_SPEC_TYPE);
        }

        $specClassName = $classMap[$type];

        return new $specClassName($specData);
    }
}