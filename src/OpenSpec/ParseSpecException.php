<?php

namespace OpenSpec;


use Throwable;

class ParseSpecException extends \RuntimeException
{
    const CODE_GENERAL_PARSER_ERROR   =  1;
    const CODE_MULTIPLE_PARSER_ERROR  =  2;
    const CODE_ARRAY_EXPECTED         =  3;
    const CODE_UNKNOWN_SPEC_TYPE      =  4;
    const CODE_INVALID_TYPE_NAME_TYPE =  5;
    const CODE_INVALID_SPEC_DATA      =  6;
    const CODE_MISSING_REQUIRED_FIELD =  7;
    const CODE_UNEXPECTED_FIELDS      =  8;
    const CODE_INVALID_TYPE_NAME      =  9;
    const CODE_EXTENSIBLE_EXPECTED    = 10;

    protected $_errors;

    public function __construct($message, $code, array $errors = null, Throwable $previous = null)
    {
        if ($code === self::CODE_MULTIPLE_PARSER_ERROR) {
            $messages = array_column($errors, 1);
            $message = $message . PHP_EOL . '- ' . implode(PHP_EOL . '- ', $messages);

            $this->_errors = $errors;
        } else {
            $this->_errors = [[$code, $message]];
        }

        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function containsError($code): bool
    {
        $errorCodes = array_column($this->_errors, 0);

        return in_array($code, $errorCodes);
    }
}
