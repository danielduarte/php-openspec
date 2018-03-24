<?php

namespace GenericEntity;


use Throwable;

class SpecException extends \RuntimeException
{
    protected $_errors;

    public function __construct($message, array $errors, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->_errors = $errors;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}