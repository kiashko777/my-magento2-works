<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Downloadable\Model\Product\Price;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var Type
     */
    protected $_productType;

    /**
     * @param string|null $typeId
     * @param string $expectedClass
     * @dataProvider factoryDataProvider
     */
    public function testFactory($typeId, $expectedClass)
    {
        $product = new DataObject();
        if ($typeId) {
            $product->setTypeId($typeId);
        }
        $type = $this->_productType->factory($product);
        $this->assertInstanceOf($expectedClass, $type);
    }

    /**
     * @return array
     */
    public function factoryDataProvider()
    {
        return [
            [null, Simple::class],
            [Type::TYPE_SIMPLE, Simple::class],
            [Type::TYPE_VIRTUAL, Virtual::class],
            [Type::TYPE_BUNDLE, \Magento\Bundle\Model\Product\Type::class],
            [
                \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
                \Magento\Downloadable\Model\Product\Type::class
            ]
        ];
    }

    /**
     * @param string|null $typeId
     * @dataProvider factoryReturnsSingletonDataProvider
     */
    public function testFactoryReturnsSingleton($typeId)
    {
        $product = new DataObject();
        if ($typeId) {
            $product->setTypeId($typeId);
        }

        $type = $this->_productType->factory($product);
        $otherType = $this->_productType->factory($product);
        $this->assertSame($otherType, $type);
    }

    /**
     * @return array
     */
    public function factoryReturnsSingletonDataProvider()
    {
        return [
            [null],
            [Type::TYPE_SIMPLE],
            [Type::TYPE_VIRTUAL],
            [Type::TYPE_BUNDLE],
            [\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE]
        ];
    }

    /**
     * @param string|null $typeId
     * @param string $expectedClass
     * @dataProvider priceFactoryDataProvider
     */
    public function testPriceFactory($typeId, $expectedClass)
    {
        $type = $this->_productType->priceFactory($typeId);
        $this->assertInstanceOf($expectedClass, $type);
    }

    public function priceFactoryDataProvider()
    {
        return [
            [null, \Magento\Catalog\Model\Product\Type\Price::class],
            [Type::TYPE_SIMPLE, \Magento\Catalog\Model\Product\Type\Price::class],
            [Type::TYPE_VIRTUAL, \Magento\Catalog\Model\Product\Type\Price::class],
            [Type::TYPE_BUNDLE, \Magento\Bundle\Model\Product\Price::class],
            [
                \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
                Price::class
            ]
        ];
    }

    public function testGetOptionArray()
    {
        $options = $this->_productType->getOptionArray();
        $this->assertArrayHasKey(Type::TYPE_SIMPLE, $options);
        $this->assertArrayHasKey(Type::TYPE_VIRTUAL, $options);
        $this->assertArrayHasKey(Type::TYPE_BUNDLE, $options);
        $this->assertArrayHasKey(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $options);
    }

    public function testGetAllOption()
    {
        $options = $this->_productType->getAllOption();
        $this->assertTrue(isset($options[0]['value']));
        $this->assertTrue(isset($options[0]['label']));
        // doesn't make sense to test other values, because the structure of resulting array is inconsistent
    }

    public function testGetAllOptions()
    {
        $options = $this->_productType->getAllOptions();
        $types = $this->_assertOptions($options);
        $this->assertContains('', $types);
    }

    /**
     * Perform assertions on type "options" structure
     *
     * @param array $options
     * @return array collected types found in options
     */
    protected function _assertOptions($options)
    {
        $this->assertIsArray($options);
        $types = [];
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $types[] = $option['value'];
        }
        $this->assertContains(Type::TYPE_SIMPLE, $types);
        $this->assertContains(Type::TYPE_VIRTUAL, $types);
        $this->assertContains(Type::TYPE_BUNDLE, $types);
        $this->assertContains(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $types);
        return $types;
    }

    public function testGetOptions()
    {
        $options = $this->_productType->getOptions();
        $this->_assertOptions($options);
    }

    /**
     * @param string $typeId
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($typeId)
    {
        $this->assertNotEmpty($this->_productType->getOptionText($typeId));
    }

    public function getOptionTextDataProvider()
    {
        return [
            [Type::TYPE_SIMPLE],
            [Type::TYPE_VIRTUAL],
            [Type::TYPE_BUNDLE],
            [\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE]
        ];
    }

    public function testGetTypes()
    {
        $types = $this->_productType->getTypes();
        $this->assertArrayHasKey(Type::TYPE_SIMPLE, $types);
        $this->assertArrayHasKey(Type::TYPE_VIRTUAL, $types);
        $this->assertArrayHasKey(Type::TYPE_BUNDLE, $types);
        $this->assertArrayHasKey(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('label', $type);
            $this->assertArrayHasKey('model', $type);
            $this->assertArrayHasKey('composite', $type);
            // possible bug: index_priority is not defined for each type
        }
    }

    public function testGetCompositeTypes()
    {
        $types = $this->_productType->getCompositeTypes();
        $this->assertIsArray($types);
        $this->assertContains(Type::TYPE_BUNDLE, $types);
    }

    public function testGetTypesByPriority()
    {
        $types = $this->_productType->getTypesByPriority();
        // collect the types and priority in the same order as the method returns
        $result = [];
        foreach ($types as $typeId => $type) {
            if (!isset($type['index_priority'])) {
                // possible bug: index_priority is not defined for each type
                $priority = 0;
            } else {
                $priority = (int)$type['index_priority'];
            }
            // non-composite must be before composite
            if (1 != $type['composite']) {
                $priority = -1 * $priority;
            }
            $result[$typeId] = $priority;
        }

        $expectedResult = $result;
        asort($expectedResult);
        $this->assertEquals($expectedResult, $result);
    }

    protected function setUp(): void
    {
        $this->_productType = Bootstrap::getObjectManager()->get(
            Type::class
        );
    }
}
