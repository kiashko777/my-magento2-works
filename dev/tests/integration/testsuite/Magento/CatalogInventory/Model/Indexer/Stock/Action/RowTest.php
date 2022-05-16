<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Action;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class RowTest
 */
class RowTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $_processor;

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductUpdate()
    {
        $categoryFactory = Bootstrap::getObjectManager()->create(
            CategoryFactory::class
        );
        /** @var ListProduct $listProduct */
        $listProduct = Bootstrap::getObjectManager()->create(
            ListProduct::class
        );

        /** @var DataObjectHelper $dataObjectHelper */
        $dataObjectHelper = Bootstrap::getObjectManager()->get(
            DataObjectHelper::class
        );

        /** @var StockRegistryInterface $stockRegistry */
        $stockRegistry = Bootstrap::getObjectManager()->create(
            StockRegistryInterface::class
        );
        /** @var StockItemRepositoryInterface $stockItemRepository */
        $stockItemRepository = Bootstrap::getObjectManager()->create(
            StockItemRepositoryInterface::class
        );

        /** @var ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );

        $product = $productRepository->get('simple');

        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $stockItem = $stockRegistry->getStockItem($product->getId(), 1);

        $this->assertNotNull($stockItem->getItemId());

        $stockItemData = [
            'qty' => $stockItem->getQty() + 11,
        ];

        $dataObjectHelper->populateWithArray(
            $stockItem,
            $stockItemData,
            StockItemInterface::class
        );
        $stockItemRepository->save($stockItem);

        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();
        $productCollection->joinField(
            'qty',
            'cataloginventory_stock_status',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $this->assertEquals(1, $productCollection->count());
        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals('Simple Products', $product->getName());
            $this->assertEquals('Short description', $product->getShortDescription());
            $this->assertEquals(111, $product->getQty());
        }
    }

    protected function setUp(): void
    {
        $this->_processor = Bootstrap::getObjectManager()->create(
            Processor::class
        );
    }
}
