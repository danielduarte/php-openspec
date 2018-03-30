<?php

namespace OpenSpec\Spec;

use OpenSpec\SpecBuilder;
use OpenSpec\ParseSpecException;


class Spec
{
    protected $_name = null;

    protected $_version = null;

    protected $_spec = null;

    protected $_definitions = [];

    public function __construct(array $specData)
    {
        if (!$this->_validateSpecData($specData)) {
            throw new ParseSpecException('Invalid spec data.', ParseSpecException::CODE_MULTIPLE_PARSER_ERROR);
        }

        $this->_name    = $specData['name'];
        $this->_version = $specData['version'];
        $this->_spec = SpecBuilder::getInstance()->build($specData['spec']);
    }

    protected function _validateSpecData($specData)
    {
        return SpecBuilder::getInstance()->build([
            'type'   => 'object',
            'fields' => [
                'openspec' => ['type' => 'string'],
                'name'     => ['type' => 'string'],
                'version'  => ['type' => 'string'],
                'spec'     => ['type' => 'mixed', 'options' => [
                    ['type' => 'null'],
                    ['type' => 'boolean'],
                    ['type' => 'string'],
                    ['type' => 'object', 'extensible' => true],
                    ['type' => 'array'],
                ]],
                'definitions' => ['type' => 'object', 'extensible' => true],
            ]
        ])->validate($specData);
    }
}
