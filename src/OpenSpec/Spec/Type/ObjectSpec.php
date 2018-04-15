<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;
use OpenSpec\Entity;


class ObjectSpec extends TypeSpec
{
    protected $_fieldSpecs = [];

    protected $_requiredFields = [];

    protected $_extensible = false;

    protected $_extensionFieldsSpec = null;

    public function getTypeName(): string
    {
        return 'object';
    }

    public function getRequiredFields(): array
    {
        return ['type'];
    }

    public function getOptionalFields(): array
    {
        return ['fields', 'extensible', 'extensionFields', 'requiredFields'];
    }

    public function isValidFieldName(string $fieldName): bool
    {
        // @todo should the string $fieldName be checked to make sure it is a valid identifier?
        return $this->_extensible || array_key_exists($fieldName, $this->_fieldSpecs);
    }

    protected function _validateFieldSpecData_fields($fieldValue): array
    {
        $errors = [];

        if (!is_array($fieldValue)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Array expected as value of 'fields' field, but " . gettype($fieldValue) . " given."];
            return $errors;
        }

        $expectedIndex = 0;
        foreach ($fieldValue as $fieldKey => $fieldSpecData) {
            try {
                $this->_fieldSpecs[$fieldKey] = SpecBuilder::getInstance()->build($fieldSpecData, $this->_library);
            } catch (ParseSpecException $ex) {
                $fieldErrors = $ex->getErrors();
                $errors = array_merge($errors, $fieldErrors);
            }

            $expectedIndex++;
        }

        return $errors;
    }

    protected function _validateFieldSpecData_extensible($fieldValue): array
    {
        $errors = [];

        if (!is_bool($fieldValue)) {
            $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Boolean expected as value of 'extensible' field, but " . gettype($fieldValue) . " given."];
            return $errors;
        }

        $this->_extensible = $fieldValue;

        return $errors;
    }

    protected function _validateFieldSpecData_extensionFields($fieldValue): array
    {
        $errors = [];

        // @todo IMPORTANT check if $this->_extensible could have not been initialized yet
        if (!$this->_extensible) {
            $errors[] = [ParseSpecException::CODE_EXTENSIBLE_EXPECTED, "Field 'extensionFields' can only be used when 'extensible' is true."];
        }

        try {
            $this->_extensionFieldsSpec = SpecBuilder::getInstance()->build($fieldValue, $this->_library);
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        return $errors;
    }

    protected function _validateFieldSpecData_requiredFields($fieldValue): array
    {
        $errors = [];

        if (!is_array($fieldValue)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Array expected as value of 'requiredFields' field, but " . gettype($fieldValue) . " given."];
            return $errors;
        }

        $expectedIndex = 0;
        foreach ($fieldValue as $index => $fieldName) {
            if ($index !== $expectedIndex) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Index in 'requiredFields' array must be integer and consecutive."];
            }

            if (!is_string($fieldName)) {
                $errors[] = [ParseSpecException::CODE_STRING_EXPECTED, "Required field name in object spec should be a string, but " . gettype($fieldValue) . " given."];
                continue;
            }

            // Note that the required fields could not be part of the 'fields' meta field, since they can be extension fields without specification
            // @todo check if required fields are in 'fields' in case the object is not extensible
            $this->_requiredFields[] = $fieldName;

            $expectedIndex++;
        }

        return $errors;
    }

    public function parse($value)
    {
        $parsedValue = [];

        $errors = [];

        if (!is_array($value)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Expected map-array as value for object spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        $specFieldKeys = array_keys($this->_fieldSpecs);

        // Check for required fields
        $missingRequiredFields = array_diff($this->_requiredFields, array_keys($value));
        if (count($missingRequiredFields) > 0) {
            $missingRequiredMetakeysStr = '\'' . implode('\', \'', $missingRequiredFields) . '\'';
            $errors[] = [ParseSpecException::CODE_MISSING_REQUIRED_FIELD, "Missing required field(s) $missingRequiredMetakeysStr in object spec."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        // Check that values follow the field specs
        foreach ($value as $fieldKey => $fieldValue) {
            $fieldHasSpec = in_array($fieldKey, $specFieldKeys, true);

            if (!$fieldHasSpec && !$this->_extensible) {
                $errors[] = [ParseSpecException::CODE_UNEXPECTED_FIELDS, "Unexpected field '$fieldKey' in value for object spec."];
                throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
            }
            if ($fieldHasSpec) {
                $fieldSpec = $this->_fieldSpecs[$fieldKey];
            } elseif ($this->_extensionFieldsSpec !== null) {
                $fieldSpec = $this->_extensionFieldsSpec;
            } else {
                $fieldSpec = $this->_getAnySpec();
            }

                $parsedValue[$fieldKey] = $fieldSpec->parse($fieldValue);
        }

        return new Entity($this, $parsedValue);
    }
}
