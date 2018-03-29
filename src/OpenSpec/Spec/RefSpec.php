<?php

namespace OpenSpec\Spec;

use OpenSpec\ParseSpecException;


class RefSpec extends Spec
{
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

        // @todo consider if it will be needed to check the existence of the referenced spec.

        return $errors;
    }

    public function validate($value): bool
    {
        // @todo implement validation
        return true;
    }
}
