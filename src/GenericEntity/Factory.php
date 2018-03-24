<?php

namespace GenericEntity;


class Factory
{
    protected $_specs = [];

    public function createSpec($name, $specMetadata)
    {
        if (array_key_exists($name, $this->_specs)) {
            throw new \RuntimeException("Spec $name already defined.");
        }

        if (!$this->_validateSpec($specMetadata)) {
            throw new \RuntimeException("Spec for $name not valid.");
        }

        $this->_specs[$name] = new Spec($specMetadata);

        return $this;
    }

    public function createEntity($name, $data)
    {
        if (array_key_exists($name, $this->_specs)) {
            throw new \RuntimeException("Spec $name does not exist.");
        }

        $spec = $this->_specs[$name];

        $entity = new Entity($spec, $data);

        return $entity;
    }

    protected function _validateSpec()
    {
        return true;

        return $this;
    }
}
