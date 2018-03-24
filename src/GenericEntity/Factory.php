<?php

namespace GenericEntity;

use GenericEntity\Spec\Native\StringSpec;
use GenericEntity\Spec\ObjectSpec;


class Factory
{
    protected $_specs = [];

    public function __construct()
    {
        // Add native specs
        $this->addSpec('string', new StringSpec());
    }

    public function createSpec($name, $specMetadata)
    {
        if (!$this->_validateSpec($specMetadata)) {
            throw new \RuntimeException("Spec for $name not valid.");
        }

        $spec = new ObjectSpec($specMetadata);

        $this->addSpec($name, $spec);

        return $this;
    }

    public function addSpec($name, $spec)
    {
        if (array_key_exists($name, $this->_specs)) {
            throw new \RuntimeException("Spec $name already defined.");
        }

        $this->_specs[$name] = $spec;

        return $this;
    }

    public function getSpec($name)
    {
        if (!array_key_exists($name, $this->_specs)) {
            throw new \RuntimeException("Spec $name does not exist.");
        }

        return $this->_specs[$name];
    }

    protected function _validateSpec()
    {
        // @todo implement method
        return true;
    }

    public function createEntity($name, $data)
    {
        $spec = $this->getSpec($name);

        $entity = new Entity($spec, $data);

        return $entity;
    }
}
