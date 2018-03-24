<?php

namespace GenericEntity;

use GenericEntity\Spec\ObjectSpec;
use GenericEntity\Spec\ArraySpec;


class Entity
{
    protected $_spec;

    protected $_data;

    public function __construct(ObjectSpec $spec, array $data)
    {
        $errors = $this->_validate($spec, $data);
        if (count($errors) > 0) {
            throw new \GenericEntity\SpecException('Not valid data for the specification.', $errors);
        }

        $this->_spec = $spec;
        $this->_data = $data;
    }

    protected function _validate($spec, $data)
    {
        $errors = [];

        $specFields = $spec->getFields();
        foreach ($specFields as $fieldKey => $fieldMetadata) {
            $hasField = array_key_exists($fieldKey, $data);
            if ($hasField) {
                $fieldValue = $data[$fieldKey];
                $fieldType  = $fieldMetadata['type'];

                // Get field spec
                if ($this->_isNativeSpec($fieldType)) {
                    $fieldSpec = FactorySingleton::getInstance()->getSpec($fieldType);
                } elseif ($fieldType === 'array') {
                    $fieldSpec = new ArraySpec($fieldMetadata);
                } elseif ($fieldType === 'object') {
                    $fieldSpec = new ObjectSpec($fieldMetadata);
                }

                if (!$fieldSpec->validate($fieldValue)) {
                    $errors[] = "Invalid value for field $fieldKey.";
                }
            }
        }

        $invalidFields = array_keys(array_diff_key($data, $specFields));
        if (count($invalidFields) > 0) {
            // Generate error message
            $invalidFieldsStr = '\'' . implode('\', \'', $invalidFields) . '\'';
            $validFieldsStr   = '\'' . implode('\', \'', array_keys($specFields)) . '\'';
            if (count($invalidFields) === 1) {
                $error = "Invalid field $invalidFieldsStr";
            } else {
                $error = "Invalid fields $invalidFieldsStr";
            }
            $error .= " (valid fields are $validFieldsStr).";

            $errors[] = $error;
        }

        return $errors;
    }

    protected function _isNativeSpec($fieldType)
    {
        return in_array($fieldType, ['string']);
    }
}
