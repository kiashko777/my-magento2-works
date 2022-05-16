<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Indexer\Product\Eav\Action;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Rows reindex Test
 */
class RowsTest extends TestCase
{
    /**
     * @var Action
     */
    protected $_productAction;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testUpdateAttributes()
    {
        /** @var Attribute $attr * */
        $attr = Bootstrap::getObjectManager()->get(Config::class)
            ->getAttribute('catalog_product', 'weight');
        $attr->setIsFilterable(1)->save();

        $this->assertTrue($attr->isIndexable());

        $this->_productAction->updateAttributes(
            [1],
            ['weight' => 11],
            1
        );

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
        $productCollection->addAttributeToSelect('weight');

        $this->assertCount(1, $productCollection);

        /** @var $product Product */
        foreach ($productCollection as $product) {
            $this->assertEquals('Simple Products', $product->getName());
            $this->assertEquals('Short description', $product->getShortDescription());
            $this->assertEquals(11, $product->getWeight());
        }
    }

    protected function setUp(): void
    {
        $this->_productAction = Bootstrap::getObjectManager()->get(
            Action::class
        );
    }
}
