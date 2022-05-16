<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Model\ResourceModel\Product\Indexer\Stock;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GroupedTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/GroupedProduct/_files/product_grouped.php
     */
    public function testReindexAll()
    {
        $this->processor->reindexAll();

        /** @var CategoryFactory $categoryFactory */
        $categoryFactory = Bootstrap::getObjectManager()->get(
            CategoryFactory::class
        );
        $category = $categoryFactory->create()->load(2);
        /** @var Collection $productCollection */
        $productCollection = Bootstrap::getObjectManager()->get(
            Collection::class
        );

        $productCollection->addAttributeToSelect('name');
        $productCollection->addUrlRewrite($category->getId());
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
            'Virtual Products' => 10,
            'Grouped Products' => 0
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
