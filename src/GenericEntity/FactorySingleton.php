<?php

namespace GenericEntity;


class FactorySingleton
{
    protected static $_instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): \GenericEntity\Factory
    {
        if (static::$_instance === null) {
            static::$_instance = new Factory();
        }

        return static::$_instance;
    }
}
