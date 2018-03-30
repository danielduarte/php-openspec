<?php

namespace OpenSpec\Spec;

use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


class RefSpec extends Spec
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

    public function validate($value): bool
    {
        $library = SpecLibrary::getInstance();

        if (!$library->hasSpec($this->_specName)) {
            return false;
        }

        return $library->validateValue($this->_specName, $value);
    }
}
