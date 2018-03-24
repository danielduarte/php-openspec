<?php

use PHPUnit\Framework\TestCase;


final class FactoryTest extends TestCase
{
    public function testIsSingleton(): void
    {
        $factory1 = \GenericEntity\FactorySingleton::getInstance();
        $factory2 = \GenericEntity\FactorySingleton::getInstance();

        $this->assertTrue($factory1 === $factory2);
    }

    public function testCannotAddMultipleSpecs(): void
    {
        $this->expectException(\GenericEntity\DuplicatedSpecException::class);

        $factory = \GenericEntity\FactorySingleton::getInstance();

        $factory->createSpec('duplicated', [], true);
        $factory->createSpec('duplicated', [], true);
    }

    public function testHasAllNativeTypesDefined(): void
    {
        $factory = \GenericEntity\FactorySingleton::getInstance();

        $types = \GenericEntity\Spec\Native\AbstractNativeType::getNativeTypeNames();
        foreach ($types as $type) {
            $hasType = $factory->hasSpec($type);
            $this->assertTrue($hasType, "Expected native type '$type' spec.");
        }
    }
}
