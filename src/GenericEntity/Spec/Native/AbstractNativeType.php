<?php

namespace GenericEntity\Spec\Native;

use GenericEntity\Spec\Spec;


abstract class AbstractNativeType implements Spec
{
    const NATIVE_TYPE_STRING  = 'string';

    const NATIVE_TYPE_BOOLEAN = 'boolean';

    public static function getNativeTypeNames()
    {
        return [
            self::NATIVE_TYPE_STRING,
            self::NATIVE_TYPE_BOOLEAN
        ];
    }
}
