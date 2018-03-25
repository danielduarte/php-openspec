<?php

use PHPUnit\Framework\TestCase;


final class SpecLoaderTest extends TestCase
{
    public function testLoadOpenApiSpec(): void
    {
        try {
            new \OpenApi\SpecLoader();
            $errors = [];
        } catch (\GenericEntity\SpecException $ex) {
            $errors = $ex->getErrors();
        }

        $errorCount = count($errors);
        $this->assertTrue($errorCount === 0, "Could not load OpenApi metaspec. $errorCount error(s) found." . PHP_EOL .
            '- ' . implode(PHP_EOL . '- ', $errors)
        );
    }

    public function testLoadUserSpecs(): void
    {
        $loader = new \OpenApi\SpecLoader();

        $specFilesExpr = __DIR__ . '/cases/openapi/*.yml';
        $specFiles = glob($specFilesExpr);

        $this->assertTrue(count($specFiles) > 0, "There are no file cases to test that match '$specFilesExpr'.");

        foreach ($specFiles as $filepath) {

            $errors = [];
            try {
                $apiSpec = $loader->loadFromFile($filepath);
            } catch (\GenericEntity\SpecException $ex) {
                $errors = $ex->getErrors();
            }

            $errorCount = count($errors);
            $this->assertTrue($errorCount === 0, "Could not load OpenApi user spec in file '$filepath'. $errorCount error(s) found." . PHP_EOL .
                '- ' . implode(PHP_EOL . '- ', $errors)
            );
        }
    }
}
