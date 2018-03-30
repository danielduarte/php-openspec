<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;


class BooleanSpec extends TypeSpec
{
    public function getTypeName(): string
    {
        return 'boolean';
    }

    public function getRequiredFields(): array
    {
        return ['type'];
    }

    public function getOptionalFields(): array
    {
        return [];
    }

    public function validateGetErrors($value): array
    {
        $errors = [];

        if (!is_bool($value)) {
            $errors[] = [ParseSpecException::CODE_BOOLEAN_EXPECTED, "Expected boolean value for 'boolean' type spec."];
        }

        return $errors;
    }
}
