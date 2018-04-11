<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


class OpenSpec implements Spec
{
    protected $_openspecVersion = null;

    protected $_name = null;

    protected $_version = null;

    protected $_spec = null;

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

        if (array_key_exists('definitions', $specData)) {
            $this->_parseDefinitions($specData['definitions']);
        }

        // @todo check here the missing references (refs to definitions that are not specified)
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

    protected function _parseDefinitions($definitionsData): void
    {
        foreach ($definitionsData as $defName => $defSpec) {
            SpecLibrary::getInstance()->registerSpecFromData($defName, $defSpec);
        }
    }

    public function validateGetErrors($userSpecData): array
    {
        return $this->_spec->validateGetErrors($userSpecData);
    }

    public function validate($userSpecData): bool
    {
        return count($this->validateGetErrors($userSpecData)) === 0;
    }

    public function getOpenspecVersion()
    {
        return $this->_openspecVersion;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function getSpec()
    {
        return $this->_spec;
    }
}
