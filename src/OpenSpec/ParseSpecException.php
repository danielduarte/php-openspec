<?php

namespace OpenSpec;

use RuntimeException;
use Throwable;


class ParseSpecException extends RuntimeException
{
    const CODE_GENERAL_PARSER_ERROR                    =  1;
    const CODE_MULTIPLE_PARSER_ERROR                   =  2;
    const CODE_INVALID_SPEC_DATA                       =  7;
    const CODE_UNKNOWN_SPEC_TYPE                       =  5;
    const CODE_INVALID_TYPE_NAME_TYPE                  =  6;
    const CODE_UNEXPECTED_FIELDS                       =  9;
    const CODE_MISSING_REQUIRED_FIELD                  =  8;
    const CODE_MISSING_NEEDED_FIELD                    = 18; // This is not the same as CODE_MISSING_REQUIRED_FIELD since this is for fields that are needed by other fields specified
    const CODE_INVALID_TYPE_NAME                       = 10;
    const CODE_EXTENSIBLE_EXPECTED                     = 11;
    const CODE_INVALID_REGEX_FOR_EXTENSIBLE_FIELDNAMES = 17;
    const CODE_UNDEFINED_NAMED_SPEC                    = 14;
    const CODE_NULL_EXPECTED                           = 12;
    const CODE_BOOLEAN_EXPECTED                        = 13;
    const CODE_INTEGER_EXPECTED                        = 15;
    const CODE_FLOAT_EXPECTED                          = 16;
    const CODE_STRING_EXPECTED                         =  3;
    const CODE_ARRAY_EXPECTED                          =  4;

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

        return in_array($code, $errorCodes, true);
    }
}
