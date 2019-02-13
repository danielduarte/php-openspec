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

    public function getFieldValidationDependencies(): array {
        return [];
    }

    public function parse($value)
    {
        $errors = [];

        if (!is_int($value)) {
            $errors[] = [ParseSpecException::CODE_INTEGER_EXPECTED, "Expected integer value for 'integer' type spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        return $value;
    }
}
