<?php

namespace GenericEntity\Spec;

use GenericEntity\FactorySingleton;
use GenericEntity\Spec\Native\AbstractNativeType;


class ObjectSpec extends AbstractSpec
{
    protected $_fieldsMetadata = [];

    protected $_isExtensible = false;


    protected function _getRequiredMetakeys()
    {
        return ['type', 'fields'];
    }

    protected function _getOptionalMetakeys()
    {
        return ['extensible'];
    }

    protected function _loadMetadata($metadata)
    {
        $errors = parent::_loadMetadata($metadata);


        // Process metafields
        $errorsType       = $this->_processMetafieldType($metadata);
        $errorsFields     = $this->_processMetafieldFields($metadata);
        $errorsExtensible = $this->_processMetafieldExtensible($metadata);

        $errors = array_merge($errors, $errorsType, $errorsFields, $errorsExtensible);


        return $errors;
    }

    protected function _processMetafieldType($metadata)
    {
        if (!array_key_exists('type', $metadata)) {
            return ["Not specified metakey 'type'."];
        }

        $type = $metadata['type'];
        if ($type !== 'object') {
            if (!is_string($type)) {
                return ["Expected value of 'type' to be a string, but " . gettype($type) . " value given.'"];
            } else  {
                return ["Expected value of 'type' to be 'object', but '$type' given.'"];
            }
        }

        return [];
    }

    protected function _processMetafieldFields($metadata)
    {
        if (!array_key_exists('fields', $metadata)) {
            return ["Not specified metakey 'fields'."];
        }

        $fieldsSpec = $metadata['fields'];
        if (!is_array($fieldsSpec)) {
            return ["Expected value of 'fields' to be an array, but " . gettype($fieldsSpec) . " value given.'"];
        }

        $this->_fieldsMetadata = $fieldsSpec;

        return $this->_validateMetadata($fieldsSpec);
    }

    protected function _processMetafieldExtensible($metadata)
    {
        if (!array_key_exists('extensible', $metadata)) {
            $isExtensible = false;
        } else {
            $isExtensible = $metadata['extensible'];
            if (!is_bool($isExtensible)) {
                return ["Expected value of 'extensible' to be a boolean, but " . gettype($isExtensible) . " value given.'"];
            }
        }

        $this->_isExtensible = $isExtensible;

        return [];
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
                    $fieldSpec = new ArraySpec($fieldMetadata);
                } elseif ($fieldType === 'object') {
                    $fieldSpec = new ObjectSpec($fieldMetadata);
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
        return in_array($fieldType, AbstractNativeType::getNativeTypeNames());
    }

    protected function __errInvalidFieldType($fieldType)
    {
        if (is_null($fieldType)) {
            return "Not specified field type.";
        } elseif ($fieldType === '') {
            return "Field type cannot be empty.";
        }

        return "Invalid field type '$fieldType'.";
    }

    public function isExtensible()
    {
        return $this->_isExtensible;
    }

    public function validate($value): array
    {
        return $this->_validateObjectData(
            $this->_fieldsMetadata,
            $this->isExtensible(),
            $value
        );
    }
}
