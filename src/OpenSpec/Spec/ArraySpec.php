<?php

namespace OpenSpec\Spec;


class ArraySpec extends Spec
{
    public function getTypeName(): string
    {
        return 'array';
    }

    public function getRequiredFields(): array
    {
        return ['type', 'items'];
    }

    public function getOptionalFields(): array
    {
        return [];
    }
}
