<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\SpecBuilder;


final class SpecBuilderTest extends TestCase
{
    public function testCannotCreateSpecBuilder(): void
    {
        $this->expectException(\Error::class);

        $className = 'SpecBuilder';
        new $className();
    }

    public function testSpecBuilderIsSingleton(): void
    {
        $builder1 = SpecBuilder::getInstance();
        $builder2 = SpecBuilder::getInstance();

        $this->assertInstanceOf(SpecBuilder::class, $builder1);

        $this->assertTrue($builder1 === $builder2, 'Spec Builder is not a singleton since more than one instance could be created.');
    }
}
