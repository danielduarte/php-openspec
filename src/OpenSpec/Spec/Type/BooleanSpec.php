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

    public function getFieldValidationDependencies(): array {
        return [];
    }

    public function parse($value)
    {
        $errors = [];

        if (!is_bool($value)) {
            $errors[] = [ParseSpecException::CODE_BOOLEAN_EXPECTED, "Expected boolean value for 'boolean' type spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        return $value;
    }
}
