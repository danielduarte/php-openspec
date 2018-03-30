<?php

namespace OpenSpec\Spec\Type;


class BooleanSpec extends Spec
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

    public function validate($value): bool
    {
        return is_bool($value);
    }
}
