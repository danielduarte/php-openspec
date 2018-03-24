<?php

namespace GenericEntity;


class Spec
{
    protected $_metadata = [];

    public function __construct($metadata)
    {
        $this->_metadata = $metadata;
    }
}
