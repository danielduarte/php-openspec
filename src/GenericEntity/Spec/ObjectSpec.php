<?php

namespace GenericEntity\Spec;

use GenericEntity\FactorySingleton;
use GenericEntity\SpecException;


class ObjectSpec implements Spec
{
    protected $_metadata = [];

    protected $_isExtensible = [];

    public function __construct(array $metadata, bool $isExtensible)
    {
        $this->_isExtensible = $isExtensible;
        $this->_metadata     = $metadata;

        $errors = $this->_validateMetadata($metadata);
        if (count($errors) > 0) {
            throw new SpecException('Not valid specification.', $errors);
        }
    }

    public function getFields()
    {
        return $this->_metadata;
    }

    public function isExtensible()
    {
        return $this->_isExtensible;
    }

    public function validate($value)
    {
        $specFields   = $this->getFields();
        $isExtensible = $this->isExtensible();

        return $this->_validateObjectData($specFields, $isExtensible, $value);
    }

    protected function _validateMetadata($metadata)
    {
        $errors = [];

        // Fixed specification for field specs
        $metaSpecFields = [
            'type'       => ['type' => 'string'],
            'fields'     => ['type' => 'object', 'fields' => [], 'extensible' => true],
            'extensible' => ['type' => 'boolean'],
            'items'      => ['type' => 'object', 'fields' => [], 'extensible' => true],
        ];
        $isExtensible = false;
        // End: Fixed specification for field specs

        foreach ($metadata as $fieldKey => $fieldMetadata) {
            if (!is_string($fieldKey)) {
                $errors[] = "Invalid field key '$fieldKey', field keys must be a string.";
            }

            $fieldErrors = $this->_validateObjectData($metaSpecFields, $isExtensible, $fieldMetadata);
            $errors = array_merge($errors, $fieldErrors);
        }

        return $errors;
    }

    protected function _validateObjectData($specFields, $isExtensible, $fieldValues)
    {
        $errors = [];

        if (!is_array($fieldValues)) {
            $errors[] = "Invalid field values, array expected";
            return $errors;
        }

        foreach ($specFields as $fieldKey => $fieldMetadata) {
            $hasField = array_key_exists($fieldKey, $fieldValues);
            if ($hasField) {
                $fieldValue = $fieldValues[$fieldKey];
                $fieldType  = array_key_exists('type', $fieldMetadata) ? $fieldMetadata['type'] : null;

                // Get field spec
                $fieldSpec = null;
                if ($this->_isNativeSpec($fieldType)) {
                    // @todo control that there are no more fields in $fieldMetadata
                    $fieldSpec = FactorySingleton::getInstance()->getSpec($fieldType);
                } elseif ($fieldType === 'array') {
                    $fieldSpec = $this->_createArraySpec($fieldMetadata);
                } elseif ($fieldType === 'object') {
                    $fieldSpec = $this->_createObjectSpec($fieldMetadata);
                }

                if ($fieldSpec === null) {
                    $fieldErrors = [$this->__errInvalidFieldType($fieldType)];
                } else {
                    $fieldErrors = $fieldSpec->validate($fieldValue);
                }

                $errors = array_merge($errors, $fieldErrors);
            }
        }

        if (!$isExtensible) {
            $invalidFields = array_keys(array_diff_key($fieldValues, $specFields));
            if (count($invalidFields) > 0) {
                // Generate error message
                $invalidFieldsStr = '\'' . implode('\', \'', $invalidFields) . '\'';
                $validFieldsStr = '\'' . implode('\', \'', array_keys($specFields)) . '\'';
                if (count($invalidFields) === 1) {
                    $error = "Invalid field $invalidFieldsStr";
                } else {
                    $error = "Invalid fields $invalidFieldsStr";
                }
                $error .= " (valid fields are $validFieldsStr).";

                $errors[] = $error;
            }
        }

        return $errors;
    }

    protected function _isNativeSpec($fieldType)
    {
        return in_array($fieldType, ['string', 'boolean']);
    }

    protected function _createArraySpec(array $fieldMetadata)
    {
        $errors = [];

        // Array specs metadata
        $requiredMetakeys = ['type', 'items'];
        $optionalMetakeys = [];
        $allValidMetakeys = array_merge($requiredMetakeys, $optionalMetakeys);

        $givenMetakeys = array_keys($fieldMetadata);

        // Check for required fields that are not present
        $missingRequiredMetakeys = array_diff($requiredMetakeys, $givenMetakeys);
        if (count($missingRequiredMetakeys) > 0) {
            $missingRequiredMetakeysStr = '\'' . implode('\', \'', $missingRequiredMetakeys) . '\'';
            $errors[] = "Invalid array spec. Missing required metakeys $missingRequiredMetakeysStr.";
        }

        // Check for unexpected fields
        $unexpectedMetakeys = array_diff($givenMetakeys, $allValidMetakeys);
        if (count($unexpectedMetakeys) > 0) {
            $unexpectedMetakeysStr = '\'' . implode('\', \'', $unexpectedMetakeys) . '\'';
            $errors[] = "Invalid array spec. Unexpected metakeys $unexpectedMetakeysStr.";
        }

        // Check if there were errors
        if (count($errors) > 0) {
            throw new SpecException('Invalid array spec.', $errors);
        }

        $itemsFields = $fieldMetadata['items'];
        $fieldSpec = new ArraySpec($itemsFields);

        return $fieldSpec;
    }

    protected function _createObjectSpec(array $fieldMetadata)
    {
        $errors = [];

        // Object specs metadata
        $requiredMetakeys = ['type', 'fields'];
        $optionalMetakeys = ['extensible'];
        $allValidMetakeys = array_merge($requiredMetakeys, $optionalMetakeys);

        $givenMetakeys = array_keys($fieldMetadata);

        // Check for required fields that are not present
        $missingRequiredMetakeys = array_diff($requiredMetakeys, $givenMetakeys);
        if (count($missingRequiredMetakeys) > 0) {
            $missingRequiredMetakeysStr = '\'' . implode('\', \'', $missingRequiredMetakeys) . '\'';
            $errors[] = "Invalid object spec. Missing required metakeys $missingRequiredMetakeysStr.";
        }

        // Check for unexpected fields
        $unexpectedMetakeys = array_diff($givenMetakeys, $allValidMetakeys);
        if (count($unexpectedMetakeys) > 0) {
            $unexpectedMetakeysStr = '\'' . implode('\', \'', $unexpectedMetakeys) . '\'';
            $errors[] = "Invalid object spec. Unexpected metakeys $unexpectedMetakeysStr.";
        }

        // Calculates extensibility of object spec
        if (array_key_exists('extensible', $fieldMetadata)) {
            $isExtensible = $fieldMetadata['extensible'];
            if (!is_bool($isExtensible)) {
                $errors[] = "Invalid value for 'extensible'. It must be a boolean.";
                $isExtensible = false;  // If incorrect value, defaults to false
            }
        } else {
            $isExtensible = false; // If absent, defaults to false
        }

        // Check if there were errors
        if (count($errors) > 0) {
            throw new SpecException('Invalid object spec.', $errors);
        }

        $objectFields = $fieldMetadata['fields'];
        $fieldSpec = new ObjectSpec($objectFields, $isExtensible);

        return $fieldSpec;
    }

    protected function __errInvalidFieldType($fieldType)
    {
        if (is_null($fieldType)) {
            return "Field type cannot be null.";
        } elseif ($fieldType === '') {
            return "Field type cannot be empty.";
        }

        return "Invalid field type '$fieldType'.";
    }
}
