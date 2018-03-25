<?php

namespace OpenSpec\Spec;


class MixedSpec extends Spec
{
    public function getTypeName(): string
    {
        return 'mixed';
    }

    public function getRequiredFields(): array
    {
        return ['type', 'options'];
    }

    public function getOptionalFields(): array
    {
        return [];
    }
}
