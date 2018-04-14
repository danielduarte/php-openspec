<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecLibrary;


interface Spec
{
    public function validateGetErrors($value): array;

    public function validate($value): bool;

    public function getSpecLibrary(): SpecLibrary;
}
