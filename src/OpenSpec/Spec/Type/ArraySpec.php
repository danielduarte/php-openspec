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

    public function validateGetErrors($value): array
    {
        $errors = [];

        if (!is_array($value)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Array expected as value of 'array' spec, but " . gettype($value) . " given."];
            return $errors;
        }

        if ($this->_itemsSpec === null) {
            // No errors
            return $errors;
        }

        $expectedIndex = 0;
        foreach ($value as $index => $item) {
            if ($expectedIndex !== $index) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Index in value of 'array' spec expected to be integer and consecutive."];
                return $errors;
            }

            $itemErrors = $this->_itemsSpec->validateGetErrors($item);
            if (count($itemErrors) > 0) {
                $msg = '- ' . implode(PHP_EOL . '- ', array_column($itemErrors, 1));
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Array item with index $index does not follow the spec." . PHP_EOL . $msg];
                return $errors;
            }

            $expectedIndex++;
        }

        // No errors
        return $errors;
    }
}
