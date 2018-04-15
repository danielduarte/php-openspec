<?php

use PHPUnit\Framework\TestCase;
use OpenSpec\Spec\OpenSpec;
use OpenSpec\Entity;


final class ValueParsingTest extends TestCase
{
    public function testParseValidSimpleSpec()
    {
        $specData = [
            'openspec' => '1.2.0',
            'name'     => 'Product',
            'version'  => '1.0.0',
            'spec'     => [
                'type' => 'object',
                'fields' => [
                    'name'        => ['type' => 'string'],
                    'sku'         => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'qty'         => ['type' => 'integer']
                ],
                'requiredFields' => ['name', 'sku']
            ]
        ];

        $productSpec = new OpenSpec($specData);

        $product = $productSpec->parse([
            'name' => 'Smart TV',
            'sku'  => 'smart-tv',
            'qty'  => 6
        ]);

        // @todo Review all tests with assertEquals and assertNotEquals and consider use of assertSame and assertNotSame
        $this->assertSame('Smart TV', $product->getName());
        $this->assertSame('smart-tv', $product->getSku());
        $this->assertSame(6, $product->getQty());
        $this->assertSame(null, $product->getDescription());
        $this->assertNotSame('', $product->getDescription());
    }

    public function testParseValidComplexSpec()
    {
        $specData = [
            'openspec' => '1.2.0',
            'name'     => 'Product',
            'version'  => '1.0.0',
            'spec'     => [
                'type' => 'object',
                'fields' => [
                    'name'        => ['type' => 'string'],
                    'sku'         => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'inventory'   => [
                        'type' => 'object',
                        'fields' => [
                            'qty'     => ['type' => 'integer'],
                            'inStock' => ['type' => 'boolean']
                        ],
                        'requiredFields' => ['qty', 'inStock']
                    ]
                ],
                'requiredFields' => ['name', 'sku']
            ]
        ];

        $productSpec = new OpenSpec($specData);

        $product = $productSpec->parse([
            'name' => 'Smart TV',
            'sku'  => 'smart-tv',
            'inventory'  => [
                'qty'     => 100,
                'inStock' => true
            ]
        ]);

        $this->assertSame(100, $product->getInventory()->getQty());
        $this->assertSame(true, $product->getInventory()->getInStock());
    }

    public function testParseValidArraySpec()
    {
        $specData = [
            'openspec' => '1.2.0',
            'name'     => 'Collection',
            'version'  => '1.0.0',
            'spec'     => [
                'type' => 'array',
            ]
        ];

        $productSpec = new OpenSpec($specData);

        // ---- Array 1
        $collection = $productSpec->parse([1, 2, 3]);
        $this->assertSame([1, 2, 3], $collection);

        // ---- Array 2
        $collection = $productSpec->parse([
            ['name' => 'Dani'],
            ['name' => 'David'],
            ['name' => 'Manu']
        ]);
        $this->assertSame(3, count($collection));
        $this->assertSame('Dani', $collection[0]->getName());

        // ---- Array 3
        $collection = $productSpec->parse([
            3.1415,
            [['name' => 'David'], 1, 2, true],
            [[[]]],
            ['a' => [1, 2, 'b' => ['c' => []]], 'hello']
        ]);
        $this->assertSame(4, count($collection));
        $this->assertSame('double', gettype($collection[0]));
        $this->assertTrue(is_array($collection[1]));
        $this->assertTrue(is_array($collection[2]));
        $this->assertInstanceOf(Entity::class, $collection[3]);
        $this->assertSame('David', $collection[1][0]->getName());
        $this->assertSame('hello', $collection[3]->get0());
    }
}
