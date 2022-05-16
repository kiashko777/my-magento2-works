<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model\Indexer;

use DateTime;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class RuleProductTest extends TestCase
{
    /**
     * @var IndexBuilder
     */
    protected $indexBuilder;

    /**
     * @var Rule
     */
    protected $resourceRule;

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAfterRuleCreation()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->get(
            ProductRepository::class
        );
        $product = $productRepository->get('simple');
        $product->setData('test_attribute', 'test_attribute_value')->save();
        $this->assertFalse($this->resourceRule->getRulePrice(new DateTime(), 1, 1, $product->getId()));

        // apply all rules
        $this->indexBuilder->reindexFull();

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $product->getId()));
    }

    protected function setUp(): void
    {
        $this->indexBuilder = Bootstrap::getObjectManager()->get(
            IndexBuilder::class
        );
        $this->resourceRule = Bootstrap::getObjectManager()->get(Rule::class);
    }
}
