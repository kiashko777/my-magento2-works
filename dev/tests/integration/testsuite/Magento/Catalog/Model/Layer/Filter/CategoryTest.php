<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Category.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    public function testGetResetValue()
    {
        $this->assertNull($this->_model->getResetValue());
    }

    public function testApplyNothing()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_model->apply(
            $objectManager->get(Request::class),
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->assertNull($objectManager->get(Registry::class)->registry('current_category_filter'));
    }

    public function testApply()
    {
        $objectManager = Bootstrap::getObjectManager();
        $request = $objectManager->get(Request::class);
        $request->setParam('cat', 3);
        $this->_model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );

        /** @var $category \Magento\Catalog\Model\Category */
        $category = $objectManager->get(Registry::class)->registry('current_category_filter');
        $this->assertInstanceOf(\Magento\Catalog\Model\Category::class, $category);
        $this->assertEquals(3, $category->getId());

        return $this->_model;
    }

    /**
     * @depends testApply
     */
    public function testGetResetValueApplied(Category $modelApplied)
    {
        $this->assertEquals(2, $modelApplied->getResetValue());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testGetItems()
    {
        Bootstrap::getObjectManager()
            ->get(Registry::class)->unregister('current_category_filter');
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $category->load(5);
        $layer = Bootstrap::getObjectManager()
            ->create(
                \Magento\Catalog\Model\Layer\Category::class,
                ['data' => ['current_category' => $category]]
            );
        $model = Bootstrap::getObjectManager()
            ->create(Category::class, ['layer' => $layer]);

        $objectManager = Bootstrap::getObjectManager();
        $request = $objectManager->get(Request::class);
        $request->setParam('cat', 3);
        $model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );

        /** @var $category \Magento\Catalog\Model\Category */
        $category = $objectManager->get(Registry::class)->registry('current_category_filter');
        $this->assertInstanceOf(\Magento\Catalog\Model\Category::class, $category);
        $this->assertEquals(3, $category->getId());

        $items = $model->getItems();

        $this->assertIsArray($items);
        $this->assertCount(2, $items);

        /** @var $item Item */
        $item = $items[0];

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($model, $item->getFilter());
        $this->assertEquals('Category 1.1', $item->getLabel());
        $this->assertEquals(4, $item->getValue());
        $this->assertEquals(2, $item->getCount());

        $item = $items[1];
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('Category 1.2', $item->getLabel());
        $this->assertEquals(13, $item->getValue());
        $this->assertEquals(2, $item->getCount());
    }

    protected function setUp(): void
    {
        $this->_category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $this->_category->load(5);
        $layer = Bootstrap::getObjectManager()
            ->create(
                \Magento\Catalog\Model\Layer\Category::class,
                ['data' => ['current_category' => $this->_category]]
            );
        $this->_model = Bootstrap::getObjectManager()
            ->create(Category::class, ['layer' => $layer]);
    }
}
