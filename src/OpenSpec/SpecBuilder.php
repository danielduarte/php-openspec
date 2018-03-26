<?php

namespace OpenSpec;

use OpenSpec\Spec\Spec;
use OpenSpec\Spec\StringSpec;
use OpenSpec\Spec\BooleanSpec;
use OpenSpec\Spec\ObjectSpec;
use OpenSpec\Spec\ArraySpec;
use OpenSpec\Spec\MixedSpec;


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

    public function build($specData): Spec
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

        switch ($type) {
            case 'string': {
                return new StringSpec($specData);
                break;
            }
            case 'boolean': {
                return new BooleanSpec($specData);
                break;
            }
            case 'object': {
                return new ObjectSpec($specData);
                break;
            }
            case 'array': {
                return new ArraySpec($specData);
                break;
            }
            case 'mixed': {
                return new MixedSpec($specData);
                break;
            }
            default: {
                throw new ParseSpecException("Unknown spec type '$type'.", ParseSpecException::CODE_UNKNOWN_SPEC_TYPE);
            }
        }
    }
}