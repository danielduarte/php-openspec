<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;
use OpenSpec\SpecBuilder;


class MixedSpec extends TypeSpec
{
    protected $_optionsSpec = [];

    public function getTypeName(): string
    {
        return 'mixed';
    }

    public function getRequiredFields(): array
    {
        return ['type', 'options'];
    }

    public function getOptionalFields(): array
    {
        return [];
    }

    protected function _validateFieldSpecData_options($fieldValue): array
    {
        $errors = [];

        if (!is_array($fieldValue)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Array expected as value of 'options' field, but " . gettype($fieldValue) . " given."];
            return $errors;
        }

        $expectedIndex = 0;
        foreach ($fieldValue as $index => $optionSpecData) {
            if ($index !== $expectedIndex) {
                $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Index in 'options' array must be integer and consecutive."];
            }

            try {
                $this->_optionsSpec[] = SpecBuilder::getInstance()->build($optionSpecData, $this->_library);
            } catch (ParseSpecException $ex) {
                $optionErrors = $ex->getErrors();
                $errors = array_merge($errors, $optionErrors);
            }

            $expectedIndex++;
        }

        return $errors;
    }

    public function parse($value)
    {
        $errors = [];

        foreach ($this->_optionsSpec as $optionSpec) {
            try {
                return $optionSpec->parse($value);
            } catch (ParseSpecException $ex) {
                // Continue with next option
            }
        }

        $errors[] = [ParseSpecException::CODE_INVALID_SPEC_DATA, "Value for 'mixed' spec does not follow any of the options."];
        throw new ParseSpecException('Could not parse the value', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
    }
}
