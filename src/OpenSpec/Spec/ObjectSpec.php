<?php

namespace OpenSpec\Spec;

use OpenSpec\ParseSpecException;
use OpenSpec\SpecBuilder;


class ObjectSpec extends Spec
{
    protected $_fieldSpecs = [];

    protected $_extensible = false;

    public function getTypeName(): string
    {
        return 'object';
    }

    public function getRequiredFields(): array
    {
        return ['fields', 'type'];
    }

    public function getOptionalFields(): array
    {
        return ['extensible', 'extensionFields'];
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
            if (!is_string($fieldKey)) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Field key must be a string, but integer given."];
                continue;
            }

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

        // @todo implement logic

        return $errors;
    }

    public function validate($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $specFieldKeys = array_keys($this->_fieldSpecs);

        foreach ($value as $fieldKey => $fieldValue) {
            if (!is_string($fieldKey)) {
                return false;
            }

            $fieldHasSpec = in_array($fieldKey, $specFieldKeys);

            if (!$fieldHasSpec && !$this->_extensible) {
                return false;
            }

            if ($fieldHasSpec) {
                $fieldSpec = $this->_fieldSpecs[$fieldKey];
                if (!$fieldSpec->validate($fieldValue)) {
                    return false;
                }
            }
        }

        return true;
    }
}
