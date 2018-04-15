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

    public function parse($value)
    {
        $errors = [];

        if (!is_float($value)) {
            $errors[] = [ParseSpecException::CODE_FLOAT_EXPECTED, "Expected float value for 'float' type spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        return $value;
    }
}
