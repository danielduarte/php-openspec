<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;


class NullSpec extends TypeSpec
{
    public function getTypeName(): string
    {
        return 'null';
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

        if ($value !== null) {
            $errors[] = [ParseSpecException::CODE_NULL_EXPECTED, "Expected null value for 'null' type spec."];
        }

        return $errors;
    }
}
