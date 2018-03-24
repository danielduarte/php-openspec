<?php

namespace OpenApi;

use Symfony\Component\Yaml\Yaml;


class SpecLoader
{
    protected $_spec = null;

    public function load($specFile)
    {
        $this->_spec = Yaml::parseFile($specFile);

        return $this;
    }

    public function dump()
    {
        print_r($this->_spec);

        return $this;
    }
}
