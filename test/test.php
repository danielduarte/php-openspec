<?php

require __DIR__ . '/../vendor/autoload.php';


$specFiles = glob(__DIR__ . '/cases/*.yml');

$loader = new \OpenApi\SpecLoader();
foreach ($specFiles as $filepath) {
    echo '- Analizing spec in file $filepath'. PHP_EOL;
    try {
        $apiSpec = $loader
            ->load($filepath);

        echo 'Ok' . PHP_EOL;
    } catch (\GenericEntity\SpecException $ex) {
        echo 'Errors:' . PHP_EOL;
        echo '  - ' . implode(PHP_EOL . '  - ', $ex->getErrors()) . PHP_EOL;
    }
    echo PHP_EOL;
}
