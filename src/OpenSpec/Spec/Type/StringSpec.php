<?php

namespace OpenSpec\Spec\Type;


class StringSpec extends Spec
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

    public function validate($value): bool
    {
        return is_string($value);
    }
}
