<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer;

use Exception;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Decimal;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 * @magentoAppIsolation enabled
 * @magentoDbIsolation disabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    protected $_model;

    public function testGetStateKey()
    {
        $this->assertEquals('STORE_1_CAT_4_CUSTGROUP_0', $this->_model->getStateKey());
    }

    public function testGetProductCollection()
    {
        /** @var $collection Collection */
        $collection = $this->_model->getProductCollection();
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(2, $collection->count());
        $this->assertSame($collection, $this->_model->getProductCollection());
    }

    public function testApply()
    {
        $this->_model->getState()->addFilter(
            Bootstrap::getObjectManager()->create(
                Item::class,
                [
                    'data' => [
                        'filter' => Bootstrap::getObjectManager()->create(
                            \Magento\Catalog\Model\Layer\Filter\Category::class,
                            ['layer' => $this->_model]
                        ),
                        'value' => 'expected-value-string',
                    ]
                ]
            )
        )->addFilter(
            Bootstrap::getObjectManager()->create(
                Item::class,
                [
                    'data' => [
                        'filter' => Bootstrap::getObjectManager()->create(
                            Decimal::class,
                            ['layer' => $this->_model]
                        ),
                        'value' => 1234,
                    ]
                ]
            )
        );

        $this->_model->apply();
        $this->assertEquals(
            'STORE_1_CAT_4_CUSTGROUP_0_cat_expected-value-string_decimal_1234',
            $this->_model->getStateKey()
        );

        $this->_model->apply();
        $this->assertEquals(
            'STORE_1_CAT_4_CUSTGROUP_0_cat_expected-value-string_decimal_1234_cat_expected-value-string_decimal_1234',
            $this->_model->getStateKey()
        );
    }

    public function testGetSetCurrentCategory()
    {
        $existingCategory = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $existingCategory->load(5);

        /* Category object */
        /** @var $model Layer */
        $model = Bootstrap::getObjectManager()->create(
            Category::class
        );
        $model->setCurrentCategory($existingCategory);
        $this->assertSame($existingCategory, $model->getCurrentCategory());

        /* Category id */
        $model = Bootstrap::getObjectManager()->create(
            Category::class
        );
        $model->setCurrentCategory(3);
        $actualCategory = $model->getCurrentCategory();
        $this->assertInstanceOf(\Magento\Catalog\Model\Category::class, $actualCategory);
        $this->assertEquals(3, $actualCategory->getId());
        $this->assertSame($actualCategory, $model->getCurrentCategory());

        /* Category in registry */
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_category', $existingCategory);
        try {
            $model = Bootstrap::getObjectManager()->create(
                Category::class
            );
            $this->assertSame($existingCategory, $model->getCurrentCategory());
            $objectManager->get(Registry::class)->unregister('current_category');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
        } catch (Exception $e) {
            $objectManager->get(Registry::class)->unregister('current_category');
            throw $e;
        }

        try {
            $model = Bootstrap::getObjectManager()->create(
                Category::class
            );
            $model->setCurrentCategory(new DataObject());
            $this->fail('Assign category of invalid class.');
        } catch (LocalizedException $e) {
        }

        try {
            $model = Bootstrap::getObjectManager()->create(
                Category::class
            );
            $model->setCurrentCategory(
                Bootstrap::getObjectManager()->create(
                    \Magento\Catalog\Model\Category::class
                )
            );
            $this->fail('Assign category with invalid id.');
        } catch (LocalizedException $e) {
        }
    }

    public function testGetCurrentStore()
    {
        $this->assertSame(
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore(),
            $this->_model->getCurrentStore()
        );
    }

    public function testGetState()
    {
        $state = $this->_model->getState();
        $this->assertInstanceOf(State::class, $state);
        $this->assertSame($state, $this->_model->getState());

        $state = Bootstrap::getObjectManager()->create(
            State::class
        );
        $this->_model->setState($state);
        // $this->_model->setData('state', state);
        $this->assertSame($state, $this->_model->getState());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Category::class
        );
        $this->_model->setCurrentCategory(4);
    }
}
