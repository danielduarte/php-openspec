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

    public function testCannotAddDuplicatedSpecs(): void
    {
        $this->expectException(\GenericEntity\DuplicatedSpecException::class);

        $factory = \GenericEntity\FactorySingleton::getInstance();

        $testSpec = [
            'type'       => 'object',
            'fields'     => [],
            'extensible' => true
        ];

        $factory->createSpec('duplicated', $testSpec);
        $factory->createSpec('duplicated', $testSpec);
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
