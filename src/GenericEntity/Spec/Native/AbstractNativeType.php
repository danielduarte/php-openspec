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

    public function validate($value): array
    {
        $valid = $this->_isValidValue($value);

        if (!$valid) {
            $basicTypeNameExpected = $this->_getBasicTypeName();
            return ["Expected $basicTypeNameExpected value, " . gettype($value) . " given."];
        } else {
            return [];
        }
    }

    protected abstract function _isValidValue($value);

    protected abstract function _getBasicTypeName();
}
