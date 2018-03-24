<?php

namespace GenericEntity\Spec\Native;

use GenericEntity\Spec\Spec;


class StringSpec extends AbstractNativeType
{
    public function validate($value)
    {
        $valid = is_string($value);

        if (!$valid) {
            return ['Expected string value.'];
        } else {
            return [];
        }
    }
}
