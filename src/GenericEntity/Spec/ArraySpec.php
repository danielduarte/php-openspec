<?php

namespace GenericEntity\Spec;


class ArraySpec extends AbstractSpec
{
    protected $_itemsMetadata = [];


    protected function _getRequiredMetakeys(): array
    {
        return ['type', 'items'];
    }

    protected function _getOptionalMetakeys(): array
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

        // Fixed specification for objects
        $objectMetaSpec = $this->_getObjectSpecData();;
        // End: Fixed specification for object specs
        return $this->_validateObjectData($objectMetaSpec, $itemsSpec);
    }

    public function validate($value): array
    {
        if (!is_array($value)) {
            return ['Expected array as a value, ' . gettype($value) . ' given.'];
        }

        $errors = [];

        foreach ($value as $arrayIndex => $arrayItem) {

            if (is_string($arrayIndex)) {
                // @todo check consecutive indexes. they should be always 0, 1, 2, ...
                $errors[] = "Expected integer as array index, given string '$arrayIndex'.";
            }

            //if ($this->_itemsMetadata['type'] === 'object') {
                $itemErrors = $this->_validateObjectData($this->_itemsMetadata, $arrayItem);
            //} elseif ($this->_itemsMetadata['type'] === 'array') {
            //    // @todo implement array of arrays
            //} else {
            //    // @todo implement the rest of the types
            //}

            $errors = array_merge($errors, $itemErrors);
        }

        return $errors;
    }

    protected function _getTypeName(): string
    {
        return 'array';
    }
}
