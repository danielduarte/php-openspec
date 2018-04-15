<?php

namespace OpenSpec;

use OpenSpec\Spec\Type\ObjectSpec;


class Entity
{
    protected $_spec = null;

    protected $_data = null;

    public function __construct(ObjectSpec $spec, array $data)
    {
        $this->_spec = $spec;
        $this->_data = $data;
    }

    public function __call(string $name, array $arguments)
    {
        $matches = null;
        preg_match('/^get([A-Z_0-9][a-zA-Z_0-9]*)$/', $name, $matches);
        // @todo check what happens with fields that started originally with uppercase
        $fieldName = lcfirst($matches[1]);

        if (!$this->_spec->isValidFieldName($fieldName)) {
            throw new \RuntimeException('Call to undefined method ' . Entity::class . '::' . $name);
        }

        if (array_key_exists($fieldName, $this->_data)) {
            return $this->_data[$fieldName];
        }

        // @todo Add support to define default values by field (in ObjectSpec)
        return null; // Default value for valid fields
    }
}
