<?php

namespace GenericEntity\Spec;

use GenericEntity\FactorySingleton;
use GenericEntity\Spec\Native\AbstractNativeType;


class ArraySpec extends AbstractSpec
{
    protected $_itemsMetadata = [];


    protected function _getRequiredMetakeys()
    {
        return ['type', 'items'];
    }

    protected function _getOptionalMetakeys()
    {
        return [];
    }

    protected function _loadMetadata($metadata)
    {
        $errors = parent::_loadMetadata($metadata);


        // Process metafields
        $errorsType  = $this->_processMetafieldType($metadata);
        $errorsItems = $this->_processMetafieldItems($metadata);

        $errors = array_merge($errors, $errorsType, $errorsItems);


        return $errors;
    }

    protected function _processMetafieldType($metadata)
    {
        if (!array_key_exists('type', $metadata)) {
            return ["Not specified metakey 'type'."];
        }

        $type = $metadata['type'];
        if ($type !== 'array') {
            if (!is_string($type)) {
                return ["Expected value of 'type' to be a string, but " . gettype($type) . " value given.'"];
            } else  {
                return ["Expected value of 'type' to be 'array', but '$type' given.'"];
            }
        }

        return [];
    }

    protected function _processMetafieldItems($metadata)
    {
        if (!array_key_exists('items', $metadata)) {
            return ["Not specified metakey 'items'."];
        }

        $itemsSpec = $metadata['items'];
        if (!is_array($itemsSpec)) {
            return ["Expected value of 'items' to be an array, but " . gettype($itemsSpec) . " value given.'"];
        }

        $this->_itemsMetadata = $itemsSpec;

        return $this->_validateMetadata($itemsSpec);
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

    public function validate($value): array
    {
        if (!is_array($value)) {
            return ['Expected array as a value.'];
        }

        $errors = [];

        foreach ($value as $arrayIndex => $arrayItem) {

            if (is_string($arrayIndex)) {
                $errors[] = "Expected integer as array index, given string '$arrayIndex'.";
            }

            $itemErrors = $this->_validateObjectData(
                $this->_itemsMetadata,
                false,
                $arrayItem
            );

            $errors = array_merge($errors, $itemErrors);
        }

        return $errors;
    }
}
