<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use GenericEntity\Spec\ArraySpec;
use GenericEntity\Spec\ObjectSpec;


final class SpecTest extends TestCase
{
    public function testSpecs(): void
    {
        $specFilesExpr = __DIR__ . '/cases/specs/*.spec.yml';
        $specFiles = glob($specFilesExpr);

        foreach ($specFiles as $specFilepath) {

            $specSampleFilepath = str_replace('.spec', '', $specFilepath);

            $userSpecData       = Yaml::parseFile($specFilepath);
            $userSpecSampleData = Yaml::parseFile($specSampleFilepath);

            try {

                if ($userSpecData['type'] === 'object') {
                    $userSpec = new ObjectSpec($userSpecData);
                } elseif ($userSpecData['type'] === 'array') {
                    $userSpec = new ArraySpec($userSpecData);
                }

                $specErrors = [];
            } catch (\GenericEntity\SpecException $ex) {
                $specErrors = $ex->getErrors();
            }

            $this->assertTrue(count($specErrors) === 0, "User spec not valid in file '$specSampleFilepath':" . PHP_EOL .
                '- ' . implode(PHP_EOL . '- ', $specErrors)
            );

            if (count($specErrors) === 0) {
                $errors = $userSpec->validate($userSpecSampleData);
                $this->assertTrue(count($errors) === 0, "User data in file '$specSampleFilepath' does not follow the spec in file '$specFilepath':" . PHP_EOL .
                    '- ' . implode(PHP_EOL . '- ', $errors)
                );
            }
        }
    }
}
