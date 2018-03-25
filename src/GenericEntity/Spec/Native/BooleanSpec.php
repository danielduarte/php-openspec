<?php

namespace GenericEntity\Spec\Native;


class BooleanSpec extends AbstractNativeType
{
    protected function _isValidValue($value)
    {
        return is_bool($value);
    }

    protected function _getBasicTypeName()
    {
        return 'boolean';
    }
}
