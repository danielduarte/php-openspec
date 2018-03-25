<?php

namespace OpenSpec;


use Throwable;

class ParseSpecException extends \RuntimeException
{
    const CODE_ARRAY_EXPECTED         = 1;
    const CODE_UNKNOWN_SPEC_TYPE      = 2;
    const CODE_INVALID_TYPE_NAME_TYPE = 3;
    const CODE_INVALID_SPEC_DATA      = 4;

    protected $_errors;

    public function __construct($message, $code, array $errors = null, Throwable $previous = null)
    {
        if ($errors === null) {
            $errors = [$message];
        }

        $message = 'Parsing error:' . PHP_EOL . '- ' . implode(PHP_EOL . '- ', $errors);

        parent::__construct($message, $code, $previous);

        $this->_errors = $errors;
    }
}
