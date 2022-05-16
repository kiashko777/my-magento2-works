<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogInventory\Model\Stock;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var Item
     */
    protected $_model;

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testSaveWithNullQty()
    {
        /** @var Product $product */
        $product = Bootstrap::getObjectManager()
            ->get(Product::class);

        $product->loadByAttribute('sku', 'simple');

        /** @var StockItemRepository $stockItemRepository */
        $stockItemRepository = $product = Bootstrap::getObjectManager()
            ->create(StockItemRepository::class);

        /** @var StockItemCriteriaInterface $stockItemCriteria */
        $stockItemCriteria = $product = Bootstrap::getObjectManager()
            ->create(StockItemCriteriaInterface::class);

        $savedStockItem = current($stockItemRepository->getList($stockItemCriteria)->getItems());
        $savedStockItemId = $savedStockItem->getItemId();

        $savedStockItem->setQty(null);
        $savedStockItem->save();

        $savedStockItem->setQty(2);
        $savedStockItem->save();
        $this->assertEquals('2.0000', $savedStockItem->load($savedStockItemId)->getQty());

        $savedStockItem->setQty(0);
        $savedStockItem->save();
        $this->assertEquals('0.0000', $savedStockItem->load($savedStockItemId)->getQty());

        $savedStockItem->setQty(null);
        $savedStockItem->save();

        $this->assertNull($savedStockItem->load($savedStockItemId)->getQty());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testStockStatusChangedAuto()
    {
        /** @var StockItemRepository $stockItemRepository */
        $stockItemRepository = $product = Bootstrap::getObjectManager()
            ->create(StockItemRepository::class);

        /** @var StockItemCriteriaInterface $stockItemCriteria */
        $stockItemCriteria = $product = Bootstrap::getObjectManager()
            ->create(StockItemCriteriaInterface::class);

        $savedStockItem = current($stockItemRepository->getList($stockItemCriteria)->getItems());

        $savedStockItem->setQty(1);
        $savedStockItem->save();

        $this->assertEquals(0, $savedStockItem->getStockStatusChangedAuto());

        $savedStockItem->setStockStatusChangedAutomaticallyFlag(1);
        $savedStockItem->save();
        $this->assertEquals(1, $savedStockItem->getStockStatusChangedAuto());
    }

    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/enable_qty_increments 1
     */
    public function testSetGetEnableQtyIncrements()
    {
        $this->assertFalse($this->_model->getEnableQtyIncrements());

        $this->_model->setUseConfigEnableQtyInc(true);
        $this->assertTrue($this->_model->getEnableQtyIncrements());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Item::class
        );
    }
}
