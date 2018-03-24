<?php

namespace GenericEntity\Spec;


class ArraySpec implements Spec
{
    protected $_metadata = [];

    public function __construct($metadata)
    {
        $this->_metadata = $metadata;
    }

    public function validate($value)
    {
        // @todo implement method
        return [];
    }
}
