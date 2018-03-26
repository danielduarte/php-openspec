<?php

namespace OpenSpec;

use OpenSpec\Spec\Spec;
use OpenSpec\SpecLibraryException;


class SpecLibrary
{
    private static $_instance = null;

    private $_specs = [];

    private function __construct()
    {
    }

    public static function getInstance(): SpecLibrary
    {
        if (static::$_instance === null) {
            static::$_instance = new SpecLibrary();
        }

        return static::$_instance;
    }

    public function hasSpec(string $name): bool
    {
        return array_key_exists($name, $this->_specs);
    }

    public function registerSpec(string $name, Spec $spec): SpecLibrary
    {
        if ($this->hasSpec($name)) {
            throw new SpecLibraryException();
        }

        $this->_specs[$name] = $spec;

        return $this;
    }

    public function registerSpecFromData(string $name, array $specData): SpecLibrary
    {
        $spec = SpecBuilder::getInstance()->build($specData);

        return $this->registerSpec($name, $spec);
    }

    public function unregisterSpec($name)
    {
        if (!$this->hasSpec($name)) {
            throw new SpecLibraryException();
        }

        $spec = $this->_specs[$name];
        unset($this->_specs[$name]);

        return $spec;
    }

    public function getSpecsCount(): int
    {
        return count($this->_specs);
    }

    public function unregisterAll()
    {
        $this->_specs = [];
    }
}
