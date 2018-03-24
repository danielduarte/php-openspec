<?php

namespace OpenApi;

use GenericEntity\FactorySingleton;
use Symfony\Component\Yaml\Yaml;


class SpecLoader
{
    const OPENAPI_SPEC_NAME = 'OpenApi';

    protected $_openApiSpec = null;

    public function __construct()
    {
        $this->_openApiSpec = $this->_loadOpenApiSpec();
    }

    public function load($specFile)
    {
        $userSpec = Yaml::parseFile($specFile);
        return $this->_parse($userSpec);
    }

    protected function _parse($userSpec)
    {
        /**
         * @var \GenericEntity\Factory
         */
        $factory = FactorySingleton::getInstance();

        $factory->createEntity(static::OPENAPI_SPEC_NAME, $userSpec);
    }

    protected function _loadOpenApiSpec()
    {
        $entityFactory = FactorySingleton::getInstance();

        $metaSpecOpenApi = [
            'openapi'      => ['type' => 'string'],
            'info'         => ['type' => 'object'],
            'servers'      => ['type' => 'array'],
            'paths'        => ['type' => 'object'],
            'components'   => ['type' => 'object'],
            'security'     => ['type' => 'array'],
            'tags'         => ['type' => 'array'],
            'externalDocs' => ['type' => 'object'],
        ];

        return $entityFactory->createSpec(static::OPENAPI_SPEC_NAME, $metaSpecOpenApi);
    }
}
