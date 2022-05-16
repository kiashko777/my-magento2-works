<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model\Indexer;

use DateTime;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductRuleTest extends TestCase
{
    /**
     * @var Rule
     */
    protected $resourceRule;

    /**
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppArea Adminhtml
     */
    public function testReindexAfterSuitableProductSaving()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $product = $productRepository->get('simple');
        $product->setData('test_attribute', 'test_attribute_value')->save();

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $product->getId()));
    }

    /**
     * Checks whether category price rule applies to product with visibility value "Not Visibility Individually".
     *
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_category_ids.php
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testReindexWithProductNotVisibleIndividually()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $product = $productRepository->get('simple-3');

        $indexBuilder = Bootstrap::getObjectManager()->get(
            IndexBuilder::class
        );
        $indexBuilder->reindexById($product->getId());

        $this->assertEquals(
            7.5,
            $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $product->getId()),
            "Catalog price rule doesn't apply to product with visibility value \"Not Visibility Individually\""
        );
    }

    protected function setUp(): void
    {
        $this->resourceRule = Bootstrap::getObjectManager()->get(Rule::class);

        Bootstrap::getObjectManager()->get(ProductRuleProcessor::class)
            ->getIndexer()->isScheduled(false);
    }
}
