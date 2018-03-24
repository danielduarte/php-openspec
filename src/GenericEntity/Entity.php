<?php

namespace GenericEntity;


class Entity
{
    protected $_data = [];

    public function __construct($data)
    {
        $this->_data = $data;
    }
}
