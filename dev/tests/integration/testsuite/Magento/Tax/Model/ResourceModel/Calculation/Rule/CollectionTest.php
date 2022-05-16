<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\ResourceModel\Calculation\Rule;

use Magento\Framework\Exception\LocalizedException;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Test setClassTypeFilter with correct Class Type
     *
     * @param $classType
     * @param $elementId
     * @param $expected
     *
     * @dataProvider setClassTypeFilterDataProvider
     */
    public function testSetClassTypeFilter($classType, $elementId, $expected)
    {
        $collection = $this->_objectManager->create(
            Collection::class
        );
        $collection->setClassTypeFilter($classType, $elementId);
        $this->assertMatchesRegularExpression($expected, (string)$collection->getSelect());
    }

    public function setClassTypeFilterDataProvider()
    {
        return [
            [
                ClassModel::TAX_CLASS_TYPE_PRODUCT,
                1,
                '/`?cd`?\.`?product_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/',
            ],
            [
                ClassModel::TAX_CLASS_TYPE_CUSTOMER,
                1,
                '/`?cd`?\.`?customer_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/'
            ]
        ];
    }

    /**
     * Test setClassTypeFilter with wrong Class Type
     *
     */
    public function testSetClassTypeFilterWithWrongType()
    {
        $this->expectException(LocalizedException::class);

        $collection = $this->_objectManager->create(
            Collection::class
        );
        $collection->setClassTypeFilter('WrongType', 1);
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
    }
}
