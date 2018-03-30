<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


class ObjectSpec extends TypeSpec
{
    protected $_fieldSpecs = [];

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
        return ['fields', 'extensible', 'extensionFields'];
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
            // Commented because PHP array indexes that are string representing numbers such as '1234' are converted automatically to integer values like 1234.
            /*if (!is_string($fieldKey)) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Field key must be a string, but integer given."];
                continue;
            }*/

            try {
                $this->_fieldSpecs[$fieldKey] = SpecBuilder::getInstance()->build($fieldSpecData);
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

        if (!$this->_extensible) {
            $errors[] = [ParseSpecException::CODE_EXTENSIBLE_EXPECTED, "Field 'extensionFields' can only be used when 'extensible' is true."];
        }

        try {
            $this->_extensionFieldsSpec = SpecBuilder::getInstance()->build($fieldValue);
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        return $errors;
    }

    public function validate($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $specFieldKeys = array_keys($this->_fieldSpecs);

        foreach ($value as $fieldKey => $fieldValue) {
            // Commented because PHP array indexes that are string representing numbers such as '1234' are converted automatically to integer values like 1234.
            /*if (!is_string($fieldKey)) {
                return false;
            }*/

            $fieldHasSpec = in_array($fieldKey, $specFieldKeys);

            if (!$fieldHasSpec && !$this->_extensible) {
                return false;
            }

            if ($fieldHasSpec) {
                $fieldSpec = $this->_fieldSpecs[$fieldKey];
            } else {
                $fieldSpec = $this->_extensionFieldsSpec;
            }

            if ($fieldSpec !== null && !$fieldSpec->validate($fieldValue)) {
                return false;
            }
        }

        return true;
    }
}
