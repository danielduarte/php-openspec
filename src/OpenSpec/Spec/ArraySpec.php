<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;


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
        } catch (SpecException $ex) {
            $errors = $ex->getErrors();
        }

        return $errors;
    }
}
