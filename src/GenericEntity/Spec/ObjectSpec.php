<?php

namespace GenericEntity\Spec;


class ObjectSpec implements Spec
{
    protected $_metadata = [];

    public function __construct($metadata)
    {
        $this->_metadata = $metadata;
    }

    public function getFields()
    {
        return $this->_metadata;
    }

    public function validate($value)
    {
        // @todo implement method
        return true;
    }
}
