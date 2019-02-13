<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


class ArraySpec extends TypeSpec
{
    protected $_itemsSpec = null;

    public function getTypeName(): string
    {
        return 'array';
    }

    public function getRequiredFields(): array
    {
        return ['type'];
    }

    public function getOptionalFields(): array
    {
        return ['items'];
    }

    public function getFieldValidationDependencies(): array {
        return [];
    }

    protected function _validateFieldSpecData_items($fieldValue): array
    {
        $errors = [];

        try {
            $this->_itemsSpec = SpecBuilder::getInstance()->build($fieldValue, $this->_library);
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        return $errors;
    }

    public function parse($value)
    {
        $parsedValue = [];

        $errors = [];

        if (!is_array($value)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Array expected as value of 'array' spec, but " . gettype($value) . " given."];
            throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        if ($this->_itemsSpec !== null) {
            $itemSpec = $this->_itemsSpec;
        } else {
            $itemSpec = $this->_getAnySpec();
        }

        $expectedIndex = 0;
        foreach ($value as $index => $item) {
            if ($expectedIndex !== $index) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Index in value of 'array' spec expected to be integer and consecutive."];
                throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
            }

            try {
                $parsedValue[] = $itemSpec->parse($item);
            } catch (ParseSpecException $ex) {
                $itemErrors = $ex->getErrors();

                $msg = '- ' . implode(PHP_EOL . '- ', array_column($itemErrors, 1));
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Array item with index $index does not follow the spec." . PHP_EOL . $msg];
                throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
            }

            $expectedIndex++;
        }

        // No errors
        return $parsedValue;
    }
}
