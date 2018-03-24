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
            throw new \GenericEntity\SpecException('Not valid specification.', $errors);
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
        $errors = [];

        if (!is_array($value)) {
            $errors[] = "Invalid object value.";
            return $errors;
        }

        $specFields   = $this->getFields();
        $isExtensible = $this->isExtensible();

        foreach ($specFields as $fieldKey => $fieldMetadata) {
            $hasField = array_key_exists($fieldKey, $value);
            if ($hasField) {
                $fieldValue = $value[$fieldKey];
                $fieldType  = $fieldMetadata['type'];

                // Get field spec
                if ($this->_isNativeSpec($fieldType)) {
                    // @todo control that there are no more fields in $fieldMetadata
                    $fieldSpec = FactorySingleton::getInstance()->getSpec($fieldType);
                } elseif ($fieldType === 'array') {
                    $fieldSpec = $this->_createArraySpec($fieldMetadata);
                } elseif ($fieldType === 'object') {
                    $fieldSpec = $this->_createObjectSpec($fieldMetadata);
                }

                $fieldErrors = $fieldSpec->validate($fieldValue);
                $errors = array_merge($errors, $fieldErrors);
            }
        }

        if (!$isExtensible) {
            $invalidFields = array_keys(array_diff_key($value, $specFields));
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

    protected function _validateMetadata($metadata)
    {
        $errors = [];

        foreach ($metadata as $fieldKey => $fieldMetadata) {
            if (!is_string($fieldKey)) {
                $errors[] = "Invalid field key '$fieldKey', field keys must be a string.";
            }

            if (!is_array($fieldMetadata)) {
                $errors[] = "Invalid field spec for '$fieldKey', field spec must be an array.";
            } else {
                $errors = array_merge($errors, $this->_validateFieldMetadata($fieldMetadata));
            }
        }

        return $errors;
    }

    protected function _validateFieldMetadata(array $metadata)
    {
        $metaspecFields = [
            'type'       => ['type' => 'string'],
            'fields'     => ['type' => 'object', 'fields' => [], 'extensible' => true],
            'extensible' => ['type' => 'boolean'],
        ];
        $isExtensible = false;

        $errors = [];
        foreach ($metaspecFields as $fieldKey => $fieldMetadata) {
            $hasField = array_key_exists($fieldKey, $metadata);
            if ($hasField) {
                $fieldValue = $metadata[$fieldKey];
                $fieldType  = $fieldMetadata['type'];

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
                    $fieldErrors = ["Invalid field type '$fieldType'."];
                } else {
                    $fieldErrors = $fieldSpec->validate($fieldValue);
                }

                $errors = array_merge($errors, $fieldErrors);
            }
        }

        if (!$isExtensible) {
            $invalidFields = array_keys(array_diff_key($metadata, $metaspecFields));
            if (count($invalidFields) > 0) {
                // Generate error message
                $invalidFieldsStr = '\'' . implode('\', \'', $invalidFields) . '\'';
                $validFieldsStr = '\'' . implode('\', \'', array_keys($metaspecFields)) . '\'';
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
        $metakeys = array_keys($fieldMetadata);
        $expectedMetakeys = ['type', 'items'];

        if (sort($metakeys) !== sort($expectedMetakeys)) {
            $expectedMetakeysStr = '\'' . implode('\', \'', $expectedMetakeys) . '\'';
            throw new SpecException("Invalid array spec. Expected $expectedMetakeysStr.");
        }

        $items = $fieldMetadata['items'];
        $fieldSpec = new ArraySpec($items);

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

        // Check for unexpected fields, if the object is not extensible
        $unexpectedMetakeys = array_diff($givenMetakeys, $allValidMetakeys);
        if (count($unexpectedMetakeys) > 0) {
            $unexpectedMetakeysStr = '\'' . implode('\', \'', $unexpectedMetakeys) . '\'';
            $errors[] = "Invalid object spec. Unexpected metakeys $unexpectedMetakeysStr.";
        }

        // Calculates extensibility of objecy spec
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
}
