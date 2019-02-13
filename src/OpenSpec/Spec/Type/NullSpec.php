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

    public function getFieldValidationDependencies(): array {
        return [];
    }

    public function parse($value)
    {
        $errors = [];

        if ($value !== null) {
            $errors[] = [ParseSpecException::CODE_NULL_EXPECTED, "Expected null value for 'null' type spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        return $value;
    }
}
