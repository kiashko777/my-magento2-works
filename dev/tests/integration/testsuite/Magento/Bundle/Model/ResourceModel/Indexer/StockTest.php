<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\ResourceModel\Indexer;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StockTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Bundle/_files/product_in_category.php
     */
    public function testReindexAll()
    {
        $this->processor->reindexAll();

        $categoryFactory = Bootstrap::getObjectManager()->get(
            CategoryFactory::class
        );
        /** @var ListProduct $listProduct */
        $listProduct = Bootstrap::getObjectManager()->get(
            ListProduct::class
        );

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

        $this->assertCount(3, $productCollection);

        $expectedResult = [
            'Simple Products' => 22,
            'Custom Design Simple Products' => 24,
            'Bundle Products' => 0
        ];

        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($expectedResult[$product->getName()], $product->getQty());
        }
    }

    protected function setUp(): void
    {
        $this->processor = Bootstrap::getObjectManager()->get(
            Processor::class
        );
    }
}
