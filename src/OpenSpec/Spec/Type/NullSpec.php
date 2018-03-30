<?php

namespace OpenSpec\Spec\Type;


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

    public function validate($value): bool
    {
        return $value === null;
    }
}
