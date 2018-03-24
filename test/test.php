<?php

require __DIR__ . '/../vendor/autoload.php';


$specFile = __DIR__ . '/sample.yml';

$loader = (new \OpenApi\SpecLoader())
    ->load($specFile)
    ->dump();
