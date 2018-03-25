<?php

namespace GenericEntity\Spec;


class ObjectSpec extends AbstractSpec
{
    protected $_fieldsMetadata = [];

    protected $_isExtensible = false;


    protected function _getRequiredMetakeys(): array
    {
        return ['type', 'fields'];
    }

    protected function _getOptionalMetakeys(): array
    {
        return ['extensible'];
    }

    protected function _loadMetadata($metadata)
    {
        $errors = parent::_loadMetadata($metadata);


        // Process metafields
        $errorsType       = $this->_processMetafieldType($metadata);
        $errorsExtensible = $this->_processMetafieldExtensible($metadata);
        $errorsFields     = $this->_processMetafieldFields($metadata);

        $errors = array_merge($errors, $errorsType, $errorsFields, $errorsExtensible);


        return $errors;
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

        return $this->_validateFieldsMetadata($fieldsSpec);
    }

    public function validate($value): array
    {
        return $this->_validateObjectData([
                'type'       => 'object',
                'fields'     => $this->_fieldsMetadata,
                'extensible' => $this->_isExtensible,
            ],
            $value
        );
    }

    protected function _getTypeName(): string
    {
        return 'object';
    }
}
