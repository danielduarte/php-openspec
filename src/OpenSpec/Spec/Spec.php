<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


class Spec
{
    protected $_openspecVersion = null;

    protected $_name = null;

    protected $_version = null;

    protected $_spec = null;

    protected $_definitions = [];

    public function __construct(array $specData)
    {
        $errors = $this->_validateSpecData($specData);
        if (count($errors) > 0) {
            throw new ParseSpecException('Invalid spec data.', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        // @todo Improve performance reusing specs already created in previous validation
        $this->_openspecVersion = $specData['openspec'];
        $this->_name            = $specData['name'];
        $this->_version         = array_key_exists('version', $specData) ? $specData['version'] : null;
        $this->_spec            = SpecBuilder::getInstance()->build($specData['spec']);
        $this->_definitions     = array_key_exists('definitions', $specData) ? $this->_parseDefinitions($specData['definitions']) : null;
    }

    protected function _validateSpecData($specData): array
    {
        // Validate metamodel
        return SpecBuilder::getInstance()->build([
            'type'   => 'object',
            'fields' => [
                'openspec'    => ['type' => 'string'],
                'name'        => ['type' => 'string'],
                'version'     => ['type' => 'string'],
                'spec'        => ['type' => 'object', 'extensible' => true],
                'definitions' => ['type' => 'object', 'extensible' => true],
            ],
            'requiredFields' => ['openspec', 'name', 'spec']
        ])->validateGetErrors($specData);
    }

    protected function _parseDefinitions($definitionsData): array
    {
        foreach ($definitionsData as $defName => $defSpec) {
            SpecLibrary::getInstance()->registerSpecFromData($defName, $defSpec);
        }
    }
}
