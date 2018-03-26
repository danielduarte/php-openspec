<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


class ArraySpec extends Spec
{
    protected $_itemsSpec = null;

    public function getTypeName(): string
    {
        return 'array';
    }

    public function getRequiredFields(): array
    {
        return ['type', 'items'];
    }

    public function getOptionalFields(): array
    {
        return [];
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
