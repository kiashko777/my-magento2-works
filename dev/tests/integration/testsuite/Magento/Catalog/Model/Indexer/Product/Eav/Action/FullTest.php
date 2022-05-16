<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Indexer\Product\Eav\Action;

use LogicException;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Indexer\Product\Eav\Processor;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

/**
 * Full reindex Test
 */
class FullTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $_processor;

    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAll()
    {
        /** @var Attribute $attr * */
        $attr = Bootstrap::getObjectManager()->create(Config::class)
            ->getAttribute('catalog_product', 'weight');
        $attr->setIsFilterable(1)->save();

        $this->assertTrue($attr->isIndexable());

        $priceIndexerProcessor = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Indexer\Product\Price\Processor::class
        );
        $priceIndexerProcessor->reindexAll();

        $this->_processor->reindexAll();

        $categoryFactory = Bootstrap::getObjectManager()->create(
            CategoryFactory::class
        );
        /** @var ListProduct $listProduct */
        $listProduct = Bootstrap::getObjectManager()->create(
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
            $this->assertEquals(1, $product->getWeight());
        }
    }

    protected function setUp(): void
    {
        $this->_processor = Bootstrap::getObjectManager()->create(
            Processor::class
        );
    }
}
