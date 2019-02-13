<?php

namespace OpenSpec\Spec\Type;

use OpenSpec\ParseSpecException;
use OpenSpec\Spec\Spec;
use OpenSpec\SpecBuilder;
use OpenSpec\SpecLibrary;


abstract class TypeSpec extends Spec
{
    protected $_anySpec = null;

    public function __construct(array $specData, SpecLibrary $library)
    {
        $this->_library = $library;

        $this->_specData = $specData;
        $errors = $this->_validateSpecData($specData);

        if (count($errors) > 0) {
            throw new ParseSpecException('Invalid spec data.', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }
    }

    public abstract function getTypeName(): string;

    public abstract function getRequiredFields(): array;

    public abstract function getOptionalFields(): array;

    public abstract function getFieldValidationDependencies(): array;

    protected function _getAnySpec()
    {
        if ($this->_anySpec === null) {
            $anySpecData = [
                'type' => 'mixed',
                'options' => [
                    ['type' => 'null'],
                    ['type' => 'boolean'],
                    ['type' => 'string'],
                    ['type' => 'integer'],
                    ['type' => 'float'],
                    ['type' => 'array'], // Array option must be before object, to avoid generating objects when they are "usual" arrays.
                    ['type' => 'object', 'extensible' => true],
                ],
            ];

            $this->_anySpec = SpecBuilder::getInstance()->build($anySpecData, $this->_library);
        }

        return $this->_anySpec;
    }

    public function getAllFields(): array
    {
        $reqFields = $this->getRequiredFields();
        $optFields = $this->getOptionalFields();

        return array_merge($reqFields, $optFields);
    }

    protected function _validateSpecData($specData)
    {
        $errors = [];

        // Check if $specData is an array
        if (!is_array($specData)) {
            $errors[] = [ParseSpecException::CODE_ARRAY_EXPECTED, "Expected array with spec data, '" . gettype($specData) . "' given."];
            return $errors;
        }

        // Check for required fields that are not present
        $givenFields = array_keys($specData);
        $requiredFields = $this->getRequiredFields();
        $missingRequiredFields = array_diff($requiredFields, $givenFields);
        if (count($missingRequiredFields) > 0) {
            $missingRequiredMetakeysStr = '\'' . implode('\', \'', $missingRequiredFields) . '\'';
            $errors[] = [ParseSpecException::CODE_MISSING_REQUIRED_FIELD, "Missing required field(s) $missingRequiredMetakeysStr in type spec '" . $this->getTypeName() . "'."];
        }

        // Check for unexpected fields
        $allValidFields = $this->getAllFields();
        $unexpectedFields = array_diff($givenFields, $allValidFields);
        if (count($unexpectedFields) > 0) {
            $unexpectedFieldsStr = '\'' . implode('\', \'', $unexpectedFields) . '\'';
            $errors[] = [ParseSpecException::CODE_UNEXPECTED_FIELDS, "Invalid spec data. Unexpected field(s) $unexpectedFieldsStr."];
        }

        // Check for specific fields according to type implementations
        $validGivenFields = array_diff($givenFields, $unexpectedFields);
        $fieldsErrors = $this->_validateFieldsSpecData($specData, $validGivenFields);
        $errors = array_merge($errors, $fieldsErrors);

        return $errors;
    }

    protected function _validateFieldsSpecData($specData, $fields)
    {
        $errors = [];

        $depends = array_filter($this->getFieldValidationDependencies());

        $pendingFields = $fields;
        $pendingCount = count($pendingFields);

        while ($pendingCount > 0) {

            $prevPendingCount = $pendingCount;

            $fieldsToProcess = $pendingFields;
            $pendingFields = [];
            foreach ($fieldsToProcess as $field) {

                // Check if the field has no dependencies
                if (!array_key_exists($field, $depends)) {

                    // If the field is independent or has all dependencies satisfied, run its validation
                    $fieldValidationMethodName = '_validateFieldSpecData_' . $field;
                    $fieldErrors = $this->$fieldValidationMethodName($specData[$field]);
                    $errors = array_merge($errors, $fieldErrors);

                    // Mark the processed field as satisfied dependence for other fields not processed yet
                    array_walk($depends, function (&$fieldDeps) use ($field) {
                        $fieldDeps = array_diff($fieldDeps, [$field]);
                    });

                    // Cleanup to remove empty dependencies
                    $depends = array_filter($depends);

                } else {

                    // If the field has unsatisfied dependencies, it is put back to the processing list
                    $pendingFields[] = $field;
                }
            }

            // Check if at least one field was processed. If not, it means a cyclic reference was detected
            $pendingCount = count($pendingFields);
            if ($pendingCount === $prevPendingCount) {
                throw new \Exception('Cyclic reference found in field dependences.');
            }
        }

        return $errors;
    }

    protected function _validateFieldSpecData_type($fieldValue): array
    {
        $errors = [];
        $expectedValue = $this->getTypeName();
        if ($fieldValue !== $expectedValue) {
            $errors[] = [ParseSpecException::CODE_INVALID_TYPE_NAME, "Expected 'type' field to have the value '$expectedValue', but '$fieldValue' given.'"];
        }

        return $errors;
    }
}
