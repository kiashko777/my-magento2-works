<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Model;

use Magento\CatalogImportExport\Model\Export\Product;
use Magento\CustomerImportExport\Model\Export\Address;
use Magento\CustomerImportExport\Model\Export\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ExportTest extends TestCase
{
    /**
     * Model object which used for tests
     *
     * @var Export
     */
    protected $_model;

    /**
     * Test method '_getEntityAdapter' in case when entity is valid
     *
     * @param string $entity
     * @param string $expectedEntityType
     * @dataProvider getEntityDataProvider
     * @covers       \Magento\ImportExport\Model\Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithValidEntity($entity, $expectedEntityType)
    {
        $this->_model->setData(['entity' => $entity]);
        $this->_model->getEntityAttributeCollection();
        $this->assertClassHasAttribute('_entityAdapter', get_class($this->_model));
        $object = new ReflectionClass(get_class($this->_model));
        $attribute = $object->getProperty('_entityAdapter');
        $attribute->setAccessible(true);
        $propertyObject = $attribute->getValue($this->_model);
        $attribute->setAccessible(false);
        $this->assertInstanceOf($expectedEntityType, $propertyObject);
    }

    /**
     * @return array
     */
    public function getEntityDataProvider()
    {
        return [
            'product' => [
                '$entity' => 'catalog_product',
                '$expectedEntityType' => Product::class,
            ],
            'customer main data' => [
                '$entity' => 'customer',
                '$expectedEntityType' => Customer::class,
            ],
            'customer address' => [
                '$entity' => 'customer_address',
                '$expectedEntityType' => Address::class,
            ]
        ];
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is invalid
     *
     * @covers \Magento\ImportExport\Model\Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithInvalidEntity()
    {
        $this->expectException(LocalizedException::class);

        $this->_model->setData(['entity' => 'test']);
        $this->_model->getEntityAttributeCollection();
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Export::class
        );
    }
}
