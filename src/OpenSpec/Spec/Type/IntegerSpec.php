<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;


class IntegerSpec extends TypeSpec
{
    public function getTypeName(): string
    {
        return 'integer';
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

        if (!is_int($value)) {
            $errors[] = [ParseSpecException::CODE_INTEGER_EXPECTED, "Expected integer value for 'integer' type spec, but " . gettype($value) . " given."];
        }

        return $errors;
    }
}
