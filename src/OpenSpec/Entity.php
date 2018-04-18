<?php

namespace OpenSpec;

use OpenSpec\Spec\Type\ObjectSpec;


class Entity
{
    protected $_spec = null;

    protected $_data = null;

    public function __construct(ObjectSpec $spec, array $data)
    {
        // @todo check if this validation should be here
//        $errors = $spec->validate($data);
//        if (count($errors) > 0) {
//            throw new \GenericEntity\SpecException('Not valid data for the specification.', $errors);
//        }

        $this->_spec = $spec;
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function __call(string $name, array $arguments)
    {
        $matches = null;
        preg_match('/^get([A-Z_0-9][a-zA-Z_0-9]*)$/', $name, $matches);
        // @todo check what happens with fields that started originally with uppercase
        $fieldName = lcfirst($matches[1]);

        return $this->getFieldData($fieldName);
    }

    public function getFieldData($fieldName)
    {
        if (!$this->isValidFieldName($fieldName)) {
            throw new \RuntimeException("Field $fieldName not exists.");
        }

        if (array_key_exists($fieldName, $this->_data)) {
            return $this->_data[$fieldName];
        }

        // @todo Add support to define default values by field (in ObjectSpec)
        return null; // Default value for valid fields
    }

    public function isValidFieldName($fieldName)
    {
        return $this->_spec->isValidFieldName($fieldName);
    }
}
