<?php

namespace GenericEntity\Spec;

use GenericEntity\SpecException;


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

    public function getOriginalMetadata()
    {
        return $this->_originalMetadata;
    }

    protected function _getAllValidMetakeys()
    {
        $requiredMetakeys = $this->_getRequiredMetakeys();
        $optionalMetakeys = $this->_getOptionalMetakeys();

        return array_merge($requiredMetakeys, $optionalMetakeys);
    }

    protected abstract function _getRequiredMetakeys();

    protected abstract function _getOptionalMetakeys();
}
