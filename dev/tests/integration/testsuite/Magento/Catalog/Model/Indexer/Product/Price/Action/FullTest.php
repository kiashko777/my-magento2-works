<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Full reindex Test
 */
class FullTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $_processor;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAll()
    {
        $this->_processor->reindexAll();

        $categoryFactory = Bootstrap::getObjectManager()->get(
            CategoryFactory::class
        );
        $listProduct = Bootstrap::getObjectManager()->get(
            ListProduct::class
        );

        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertCount(1, $productCollection);

        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals('Simple Products', $product->getName());
            $this->assertEquals('Short description', $product->getShortDescription());
            $this->assertEquals(10, $product->getPrice());
        }
    }

    protected function setUp(): void
    {
        $this->_processor = Bootstrap::getObjectManager()->get(
            Processor::class
        );
    }
}
