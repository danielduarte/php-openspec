<?php

require __DIR__ . '/../vendor/autoload.php';


echo '- Loading OpenApi 3.0.0 spec'. PHP_EOL;
try {
    $loader = new \OpenApi\SpecLoader();
    echo 'Ok' . PHP_EOL;
} catch (\GenericEntity\SpecException $ex) {
    echo 'Errors:' . PHP_EOL;
    echo '  - ' . implode(PHP_EOL . '  - ', $ex->getErrors()) . PHP_EOL;
    exit(0);
}
echo PHP_EOL;

$specFiles = glob(__DIR__ . '/unit/cases/openapi/*.yml');
foreach ($specFiles as $filepath) {
    echo "- Analizing spec in file $filepath". PHP_EOL;
    try {
        $apiSpec = $loader
            ->loadFromFile($filepath);

        echo 'Ok' . PHP_EOL;
    } catch (\GenericEntity\SpecException $ex) {
        echo 'Errors:' . PHP_EOL;
        echo '  - ' . implode(PHP_EOL . '  - ', $ex->getErrors()) . PHP_EOL;
    }
    echo PHP_EOL;
}
