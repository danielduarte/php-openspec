<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecLibrary;
use OpenSpec\SpecBuilder;
use OpenSpec\SpecLibraryException;
use OpenSpec\ParseSpecException;


final class SpecLibraryTest extends TestCase
{
    public function testCannotCreateSpecLibrary(): void
    {
        $this->expectException(\Error::class);

        $className = 'SpecLibrary';
        new $className();
    }

    public function testRegisterSpec()
    {
        $library = new SpecLibrary();
        $specName = 'TestSpec';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string'], $library);

        $this->assertTrue(!$library->hasSpec($specName));

        $library->registerSpec($specName, $spec);

        $this->assertTrue($library->hasSpec($specName));
    }

    public function testDuplicatedRegisterSpec()
    {
        $this->expectException(SpecLibraryException::class);

        $library = new SpecLibrary();
        $specName = 'TestSpec';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string'], $library);

        $library->registerSpec($specName, $spec);
        $library->registerSpec($specName, $spec);
    }

    public function testRegisterSpecFromData()
    {
        $library = new SpecLibrary();
        $specName = 'TestSpecFromData';
        $specData = ['type' => 'string'];

        $this->assertTrue(!$library->hasSpec($specName));

        $library->registerSpecFromData($specName, $specData);

        $this->assertTrue($library->hasSpec($specName));
    }

    public function testTryRegisterInvalidSpecFromData()
    {
        $this->expectException(ParseSpecException::class);

        $library = new SpecLibrary();
        $specName = 'TestInvalidSpecFromData';
        $specData = ['type' => 'hello'];

        $library->registerSpecFromData($specName, $specData);

        $this->assertTrue(!$library->hasSpec($specName));
    }

    public function testUnregisterSpec()
    {
        $library = new SpecLibrary();
        $specName = 'TestSpecToUnregister';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string'], $library);

        $library->registerSpec($specName, $spec);
        $library->unregisterSpec($specName);

        $this->assertTrue(!$library->hasSpec($specName));
    }

    public function testUnregisterNotRegisteredSpec()
    {
        $this->expectException(SpecLibraryException::class);

        $library = new SpecLibrary();
        $specName = 'TestUnregisteredSpec';

        $library->unregisterSpec($specName);
    }

    public function testReturnUnregisteredSpec()
    {
        $library = new SpecLibrary();
        $specName = 'TestSpecToUnregisterAndReturn';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string'], $library);

        $library->registerSpec($specName, $spec);
        $unregisteredSpec = $library->unregisterSpec($specName);

        $this->assertTrue($spec === $unregisteredSpec);
    }

    public function testUnregisterAllSpecs()
    {
        $library = new SpecLibrary();

        $library->registerSpecFromData('Spec1', ['type' => 'string']);
        $library->registerSpecFromData('Spec2', ['type' => 'string']);
        $library->registerSpecFromData('Spec3', ['type' => 'string']);

        $this->assertTrue($library->getSpecsCount() > 0);

        $library->unregisterAll();

        $this->assertTrue($library->getSpecsCount() === 0);
    }
}
