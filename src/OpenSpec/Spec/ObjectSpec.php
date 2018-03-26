<?php

namespace OpenSpec\Spec;

use OpenSpec\ParseSpecException;


class ObjectSpec extends Spec
{
    protected $_fieldSpec = [];

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
        return ['extensible'];
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
                $this->_fieldSpec[] = SpecBuilder::getInstance()->build($fieldSpecData);
            } catch (SpecException $ex) {
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
}
