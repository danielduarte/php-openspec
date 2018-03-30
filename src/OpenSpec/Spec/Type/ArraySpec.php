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
            $this->_itemsSpec = SpecBuilder::getInstance()->build($fieldValue);
        } catch (ParseSpecException $ex) {
            $errors = $ex->getErrors();
        }

        return $errors;
    }

    public function validate($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if ($this->_itemsSpec === null) {
            return true;
        }

        $expectedIndex = 0;
        foreach ($value as $index => $item) {
            if ($expectedIndex !== $index) {
                return false;
            }

            if (!$this->_itemsSpec->validate($item)) {
                return false;
            }

            $expectedIndex++;
        }

        return true;
    }
}
