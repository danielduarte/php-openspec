<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;
use OpenSpec\SpecLibrary;


class OpenSpec extends Spec
{
    protected $_openspecVersion = null;

    protected $_name = null;

    protected $_version = null;

    protected $_spec = null;

    public function __construct(array $specData)
    {
        $this->_library = new SpecLibrary();

        $this->_specData = $specData;
        $errors = $this->_validateSpecData($specData);
        if (count($errors) > 0) {
            throw new ParseSpecException('Invalid spec data.', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR, $errors);
        }

        // @todo Improve performance reusing specs already created in previous validation
        $this->_openspecVersion = $specData['openspec'];
        $this->_name            = $specData['name'];
        $this->_version         = array_key_exists('version', $specData) ? $specData['version'] : null;
        $this->_spec            = SpecBuilder::getInstance()->build($specData['spec'], $this->_library);

        // @todo make sure this registration should happen
        $this->_library->registerSpec($this->_name, $this);

        if (array_key_exists('definitions', $specData)) {
            $this->_parseDefinitions($specData['definitions']);
        }

        // @todo check here the missing references (refs to definitions that are not specified)
    }

    protected function _validateSpecData($specData): array
    {
        // Validate metamodel
        try {
            SpecBuilder::getInstance()->build([
                'type' => 'object',
                'fields' => [
                    'openspec'    => ['type' => 'string'],
                    'name'        => ['type' => 'string'],
                    'version'     => ['type' => 'string'],
                    'spec'        => ['type' => 'object', 'extensible' => true],
                    'definitions' => ['type' => 'object', 'extensible' => true],
                ],
                'requiredFields' => ['openspec', 'name', 'spec']
            ], $this->_library)->parse($specData);
        } catch (ParseSpecException $ex) {
            return $ex->getErrors();
        }

        return [];
    }

    protected function _parseDefinitions($definitionsData): void
    {
        foreach ($definitionsData as $defName => $defSpec) {
            $this->_library->registerSpecFromData($defName, $defSpec);
        }
    }

    public function parse($userSpecData)
    {
        return $this->_spec->parse($userSpecData);
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
