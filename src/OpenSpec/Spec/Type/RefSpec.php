<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


class RefSpec extends TypeSpec
{
    protected $_specName = null;

    public function getTypeName(): string
    {
        return 'ref';
    }

    public function getRequiredFields(): array
    {
        return ['type', 'spec'];
    }

    public function getOptionalFields(): array
    {
        return [];
    }

    protected function _validateFieldSpecData_spec($fieldValue): array
    {
        $errors = [];

        if (!is_string($fieldValue)) {
            $errors[] = [ParseSpecException::CODE_STRING_EXPECTED, "String expected as value of 'spec' field of reference, but " . gettype($fieldValue) . " given."];
            return $errors;
        }

        $this->_specName = $fieldValue;

        // @todo consider if it will be needed to check the existence of the referenced spec.

        return $errors;
    }

    public function validateGetErrors($value): array
    {
        $errors = [];

        $library = SpecLibrary::getInstance();

        if (!$library->hasSpec($this->_specName)) {
            $errors[] = [ParseSpecException::CODE_UNDEFINED_NAMED_SPEC, "Undefined named spec '" . $this->_specName . "'."];
            return $errors;
        }

        return $library->validateValueGetErrors($this->_specName, $value);
    }
}
