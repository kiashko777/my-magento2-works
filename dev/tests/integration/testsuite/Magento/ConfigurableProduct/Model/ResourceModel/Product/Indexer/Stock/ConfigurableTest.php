<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Model\ResourceModel\Product\Indexer\Stock;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigurableTest extends TestCase
{
    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testReindexAll()
    {
        /** @var Processor $processor */
        $processor = Bootstrap::getObjectManager()->get(
            Processor::class
        );

        $processor->reindexAll();

        /** @var CategoryFactory $categoryFactory */
        $categoryFactory = Bootstrap::getObjectManager()->get(
            CategoryFactory::class
        );
        $category = $categoryFactory->create()->load(2);
        /** @var Collection $productCollection */
        $productCollection = Bootstrap::getObjectManager()->get(
            Collection::class
        );

        $productCollection->addUrlRewrite($category->getId());
        $productCollection->addAttributeToSelect('name');
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
            'Configurable OptionOption 1' => 1000,
            'Configurable OptionOption 2' => 1000,
            'Configurable Products' => 0
        ];

        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($expectedResult[$product->getName()], $product->getQty());
        }
    }
}
