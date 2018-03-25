<?php

namespace OpenSpec\Spec;


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
}
