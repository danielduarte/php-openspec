<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;


class StringSpec extends TypeSpec
{
    public function getTypeName(): string
    {
        return 'string';
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

        if (!is_string($value)) {
            $errors[] = [ParseSpecException::CODE_STRING_EXPECTED, "Expected string value for 'string' type spec, but " . gettype($value) . " given."];
        }

        return $errors;
    }
}
