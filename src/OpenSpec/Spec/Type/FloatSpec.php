<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;


class FloatSpec extends TypeSpec
{
    public function getTypeName(): string
    {
        return 'float';
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

        if (!is_float($value)) {
            $errors[] = [ParseSpecException::CODE_FLOAT_EXPECTED, "Expected float value for 'float' type spec, but " . gettype($value) . " given."];
        }

        return $errors;
    }
}
