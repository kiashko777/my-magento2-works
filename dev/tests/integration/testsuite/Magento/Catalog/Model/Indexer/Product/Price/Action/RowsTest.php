<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class RowsTest
 */
class RowsTest extends TestCase
{
    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Processor
     */
    protected $_processor;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/price_row_fixture.php
     */
    public function testProductsUpdate()
    {
        /** @var ProductRepositoryInterface $productRepository */
        $product = Bootstrap::getObjectManager()->get(
            Product::class
        );
        /** @var CategoryLinkManagementInterface $linkManagment */
        $linkManagment = Bootstrap::getObjectManager()->get(
            CategoryLinkManagementInterface::class
        );
        $this->_product = $product->load(1);
        $linkManagment->assignProductToCategories($this->_product->getSku(), [9]);
        $this->_processor->reindexList([$this->_product->getId()]);

        $categoryFactory = Bootstrap::getObjectManager()->get(
            CategoryFactory::class
        );
        $listProduct = Bootstrap::getObjectManager()->get(
            ListProduct::class
        );

        $category = $categoryFactory->create()->load(9);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertEquals(1, $productCollection->count());
        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($this->_product->getId(), $product->getId());
            $this->assertEquals($this->_product->getPrice(), $product->getPrice());
        }
    }

    protected function setUp(): void
    {
        $this->_product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $this->_processor = Bootstrap::getObjectManager()->create(
            Processor::class
        );
    }
}
