<?php

namespace GenericEntity\Spec\Native;


class StringSpec extends AbstractNativeType
{
    protected function _isValidValue($value)
    {
        return is_string($value);
    }

    protected function _getBasicTypeName()
    {
        return 'string';
    }
}
