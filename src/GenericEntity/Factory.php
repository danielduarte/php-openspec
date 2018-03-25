<?php

namespace GenericEntity;

use GenericEntity\Spec\Native\StringSpec;
use GenericEntity\Spec\Native\BooleanSpec;
use GenericEntity\Spec\ObjectSpec;
use GenericEntity\Spec\Native\AbstractNativeType;


class Factory
{
    const META_SPEC_OBJECT_FIELD = 'MetaSpecObjectField';

    protected $_specs = [];

    public function __construct()
    {
        $this->_addNativeTypesSpecs();
        //$this->_addMetaTypesSpecs();
    }

    protected function _addNativeTypesSpecs()
    {
        $this->addSpec(AbstractNativeType::NATIVE_TYPE_STRING,  new StringSpec());
        $this->addSpec(AbstractNativeType::NATIVE_TYPE_BOOLEAN, new BooleanSpec());
    }

    public function _addMetaTypesSpecs()
    {
        // Meta Object Field specification
        $this->createSpec(static::META_SPEC_OBJECT_FIELD, [
            'type'       => 'object',
            'extensible' => false,
            'fields'     => [
                'type'       => ['type' => 'string'],
                'fields'     => ['type' => 'object', 'fields' => [], 'extensible' => true],
                'extensible' => ['type' => 'boolean'],
                'items'      => ['type' => 'object', 'fields' => [], 'extensible' => true],
            ],
        ]);
    }

    // @todo change the name of the $meta param
    public function createSpec(string $name, $specMetadata)
    {
        $spec = new ObjectSpec($specMetadata);

        $this->addSpec($name, $spec);

        return $this;
    }

    public function addSpec($name, $spec)
    {
        if (array_key_exists($name, $this->_specs)) {
            throw new \GenericEntity\DuplicatedSpecException("Spec '$name' already defined.");
        }

        $this->_specs[$name] = $spec;

        return $this;
    }

    public function hasSpec($name)
    {
        return array_key_exists($name, $this->_specs);
    }

    public function getSpec($name)
    {
        if (!$this->hasSpec($name)) {
            throw new \RuntimeException("Spec '$name' does not exist.");
        }

        return $this->_specs[$name];
    }

    public function createEntity($name, $data)
    {
        $spec = $this->getSpec($name);

        $entity = new Entity($spec, $data);

        return $entity;
    }
}
