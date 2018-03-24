<?php

namespace GenericEntity\Spec\Native;

use GenericEntity\Spec\Spec;


class BooleanSpec implements Spec
{
    public function validate($value)
    {
        $valid = is_bool($value);

        if (!$valid) {
            return ['Expected boolean value.'];
        } else {
            return [];
        }
    }
}
