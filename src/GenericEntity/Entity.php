<?php

namespace GenericEntity;

use GenericEntity\Spec\ObjectSpec;


class Entity
{
    protected $_spec;

    protected $_data;

    public function __construct(ObjectSpec $spec, array $data)
    {
        $errors = $spec->validate($data);
        if (count($errors) > 0) {
            throw new \GenericEntity\SpecException('Not valid data for the specification.', $errors);
        }

        $this->_spec = $spec;
        $this->_data = $data;
    }
}
