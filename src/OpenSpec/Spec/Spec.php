<?php

namespace OpenSpec\Spec;

use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


abstract class Spec
{
    protected $_library = null;

    protected $_specData = null;

    public function getSpecLibrary(): SpecLibrary
    {
        return $this->_library;
    }

    public function getSpecData(): array
    {
        return $this->_specData;
    }

    public function validate($value, bool $throwExceptionOnInvalid = false): bool
    {
        $errors = [];

        try {

            $this->parse($value);

        } catch (ParseSpecException $ex) {
            if ($throwExceptionOnInvalid) {
                throw $ex;
            }

            $errors = $ex->getErrors();
        }

        return count($errors) === 0;
    }

    public abstract function parse($value);
}
