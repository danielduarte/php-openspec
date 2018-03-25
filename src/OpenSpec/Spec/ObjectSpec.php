<?php

namespace OpenSpec\Spec;


class ObjectSpec extends Spec
{
    public function getTypeName(): string
    {
        return 'object';
    }

    public function getRequiredFields(): array
    {
        return ['fields', 'type'];
    }

    public function getOptionalFields(): array
    {
        return ['extensible'];
    }
}
