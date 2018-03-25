<?php

namespace GenericEntity\Spec;

use GenericEntity\SpecException;
use GenericEntity\FactorySingleton;
use GenericEntity\Spec\Native\AbstractNativeType;


abstract class AbstractSpec implements Spec
{
    protected $_originalMetadata;


    public function __construct($metadata)
    {
        $errors = $this->_loadMetadata($metadata);

        if (count($errors) > 0) {
            throw new SpecException('Not valid specification.', $errors);
        }
    }

    public function getOriginalMetadata()
    {
        return $this->_originalMetadata;
    }

    protected function _loadMetadata($metadata)
    {
        $this->_originalMetadata = $metadata;

        $errors = [];


        // Object specs metadata
        $requiredMetakeys = $this->_getRequiredMetakeys();
        $allValidMetakeys = $this->_getAllValidMetakeys();

        // Check if $metadata is an array
        if (!is_array($metadata)) {
            $errors[] = "Expected array with metadata, '" . gettype($metadata) . "' given.";
            return $errors;
        }

        $givenMetakeys = array_keys($metadata);

        // Check for required fields that are not present
        $missingRequiredMetakeys = array_diff($requiredMetakeys, $givenMetakeys);
        if (count($missingRequiredMetakeys) > 0) {
            $missingRequiredMetakeysStr = '\'' . implode('\', \'', $missingRequiredMetakeys) . '\'';
            $errors[] = "Invalid spec. Missing required metakeys $missingRequiredMetakeysStr.";
        }

        // Check for unexpected fields
        $unexpectedMetakeys = array_diff($givenMetakeys, $allValidMetakeys);
        if (count($unexpectedMetakeys) > 0) {
            $unexpectedMetakeysStr = '\'' . implode('\', \'', $unexpectedMetakeys) . '\'';
            $errors[] = "Invalid spec. Unexpected metakeys $unexpectedMetakeysStr.";
        }


        return $errors;
    }

    protected function _getObjectSpecData()
    {
        return [
            'type'       => 'object',
            'extensible' => false,
            'fields'     => [
                'type'       => ['type' => 'string'],
                'extensible' => ['type' => 'boolean'],
                'fields'     => ['type' => 'object', 'fields' => [], 'extensible' => true],
                'items'      => ['type' => 'object', 'fields' => [], 'extensible' => true],
            ],
        ];
    }

    protected function _validateFieldsMetadata($fieldsSpec)
    {
        $errors = [];

        $objectMetaSpec = $this->_getObjectSpecData();

        foreach ($fieldsSpec as $fieldKey => $fieldMetadata) {
            if (!is_string($fieldKey)) {
                $errors[] = "Invalid field key '$fieldKey', field keys must be a string.";
            }

            $fieldErrors = $this->_validateObjectData($objectMetaSpec, $fieldMetadata);
            $errors = array_merge($errors, $fieldErrors);
        }

        return $errors;
    }

    /**
     * This methods assumes $objectSpec is a valid object spec.
     *
     * @param $objectSpec
     * @param $objectInstance
     *
     * @return array
     */
    protected function _validateObjectData($objectSpec, $objectInstance)
    {
        $specFields   = $objectSpec['fields'];
        $isExtensible = array_key_exists('extensible', $objectSpec) ? $objectSpec['extensible'] : false;

        $errors = [];

        if (!is_array($objectInstance)) {
            $errors[] = "Invalid field type value, array expected but " . gettype($objectInstance) . " given.";
            return $errors;
        }

        foreach ($specFields as $fieldKey => $fieldMetadata) {
            $hasField = array_key_exists($fieldKey, $objectInstance);
            if ($hasField) {
                $fieldValue = $objectInstance[$fieldKey];
                $fieldType  = $fieldMetadata['type'];

                // Get field spec
                $fieldSpec = null;
                if ($this->_isNativeSpec($fieldType)) {
                    $fieldSpec = FactorySingleton::getInstance()->getSpec($fieldType);
                } elseif ($fieldType === 'array') {
                    $fieldSpec = new ArraySpec($fieldMetadata);
                } elseif ($fieldType === 'object') {
                    $fieldSpec = new ObjectSpec($fieldMetadata);
                }

                // @todo double check if this validation is needed. it is supposed that the spec is valid, so $fieldSpec should not be null
                //  if ($fieldSpec === null) {
                //      $fieldErrors = [$this->__errInvalidFieldType($fieldType)];
                //  } else {
                $fieldErrors = $fieldSpec->validate($fieldValue);
                //}

                $errors = array_merge($errors, $fieldErrors);
            }
        }

        if (!$isExtensible) {
            $invalidFields = array_keys(array_diff_key($objectInstance, $specFields));
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
        } else {
            // @todo check that extension fields are valid
        }

        return $errors;
    }

    protected function _processMetafieldType($metadata)
    {
        if (!array_key_exists('type', $metadata)) {
            return ["Not specified metakey 'type'."];
        }

        $type = $metadata['type'];
        $expectedTypeName = $this->_getTypeName();
        if ($type !== $expectedTypeName) {
            if (!is_string($type)) {
                return ["Expected value of 'type' to be a string, but " . gettype($type) . " value given.'"];
            } else  {
                return ["Expected value of 'type' to be '$expectedTypeName', but '$type' given.'"];
            }
        }

        return [];
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

    protected function _getAllValidMetakeys()
    {
        $requiredMetakeys = $this->_getRequiredMetakeys();
        $optionalMetakeys = $this->_getOptionalMetakeys();

        return array_merge($requiredMetakeys, $optionalMetakeys);
    }

    protected abstract function _getTypeName(): string;

    protected abstract function _getRequiredMetakeys(): array;

    protected abstract function _getOptionalMetakeys(): array;
}
