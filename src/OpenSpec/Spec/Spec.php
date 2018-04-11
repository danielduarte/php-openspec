<?php

namespace OpenSpec\Spec;


interface Spec
{
    public function validateGetErrors($value): array;

    public function validate($value): bool;
}
