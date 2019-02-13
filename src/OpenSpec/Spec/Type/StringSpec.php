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

    public function getFieldValidationDependencies(): array {
        return [];
    }

    public function parse($value)
    {
        $errors = [];

        if (!is_string($value)) {
            $errors[] = [ParseSpecException::CODE_STRING_EXPECTED, "Expected string value for 'string' type spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        return $value;
    }
}
