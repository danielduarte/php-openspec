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

    public function testSpecBuilderIsSingleton(): void
    {
        $library1 = SpecLibrary::getInstance();
        $library2 = SpecLibrary::getInstance();

        $this->assertInstanceOf(SpecLibrary::class, $library1);

        $this->assertTrue($library1 === $library2, 'Spec Library is not a singleton since more than one instance could be created.');
    }

    public function testRegisterSpec()
    {
        $library = SpecLibrary::getInstance();
        $specName = 'TestSpec';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string']);

        $this->assertTrue(!$library->hasSpec($specName));

        $library->registerSpec($specName, $spec);

        $this->assertTrue($library->hasSpec($specName));
    }

    public function testDuplicatedRegisterSpec()
    {
        $this->expectException(SpecLibraryException::class);

        $library = SpecLibrary::getInstance();
        $specName = 'TestSpec';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string']);

        $library->registerSpec($specName, $spec);
        $library->registerSpec($specName, $spec);
    }

    public function testRegisterSpecFromData()
    {
        $library = SpecLibrary::getInstance();
        $specName = 'TestSpecFromData';
        $specData = ['type' => 'string'];

        $this->assertTrue(!$library->hasSpec($specName));

        $library->registerSpecFromData($specName, $specData);

        $this->assertTrue($library->hasSpec($specName));
    }

    public function testTryRegisterInvalidSpecFromData()
    {
        $this->expectException(ParseSpecException::class);

        $library = SpecLibrary::getInstance();
        $specName = 'TestInvalidSpecFromData';
        $specData = ['type' => 'hello'];

        $library->registerSpecFromData($specName, $specData);

        $this->assertTrue(!$library->hasSpec($specName));
    }

    public function testUnregisterSpec()
    {
        $library = SpecLibrary::getInstance();
        $specName = 'TestSpecToUnregister';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string']);

        $library->registerSpec($specName, $spec);
        $library->unregisterSpec($specName);

        $this->assertTrue(!$library->hasSpec($specName));
    }

    public function testUnregisterNotRegisteredSpec()
    {
        $this->expectException(SpecLibraryException::class);

        $library = SpecLibrary::getInstance();
        $specName = 'TestUnregisteredSpec';

        $library->unregisterSpec($specName);
    }

    public function testReturnUnregisteredSpec()
    {
        $library = SpecLibrary::getInstance();
        $specName = 'TestSpecToUnregisterAndReturn';
        $spec = SpecBuilder::getInstance()->build(['type' => 'string']);

        $library->registerSpec($specName, $spec);
        $unregisteredSpec = $library->unregisterSpec($specName);

        $this->assertTrue($spec === $unregisteredSpec);
    }

    public function testUnregisterAllSpecs()
    {
        $library = SpecLibrary::getInstance();

        $library->registerSpecFromData('Spec1', ['type' => 'string']);
        $library->registerSpecFromData('Spec2', ['type' => 'string']);
        $library->registerSpecFromData('Spec3', ['type' => 'string']);

        $this->assertTrue($library->getSpecsCount() > 0);

        $library->unregisterAll();

        $this->assertTrue($library->getSpecsCount() === 0);
    }
}
