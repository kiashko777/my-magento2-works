<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model\Indexer;

use DateTime;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class IndexerBuilderTest extends TestCase
{
    /**
     * @var IndexBuilder
     */
    protected $indexerBuilder;

    /**
     * @var Rule
     */
    protected $resourceRule;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Product
     */
    protected $productSecond;

    /**
     * @var Product
     */
    protected $productThird;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexById()
    {
        $product = $this->product->loadByAttribute('sku', 'simple');
        $product->load($product->getId());
        $product->setData('test_attribute', 'test_attribute_value')->save();

        $this->indexerBuilder->reindexById($product->getId());

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $product->getId()));
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/CatalogRule/_files/simple_product_with_catalog_rule_50_percent_off_tomorrow.php
     * @magentoConfigFixture base_website general/locale/timezone Europe/Amsterdam
     * @magentoConfigFixture general/locale/timezone America/Chicago
     */
    public function testReindexByIdDifferentTimezones()
    {
        $productId = $this->productRepository->get('simple')->getId();
        $this->indexerBuilder->reindexById($productId);

        $mainWebsiteId = $this->storeManager->getWebsite('base')->getId();
        $secondWebsiteId = $this->storeManager->getWebsite('test')->getId();
        $rawTimestamp = (new DateTime('+1 day'))->getTimestamp();
        $timestamp = $rawTimestamp - ($rawTimestamp % (60 * 60 * 24));
        $mainWebsiteActiveRules =
            $this->resourceRule->getRulesFromProduct($timestamp, $mainWebsiteId, 1, $productId);
        $secondWebsiteActiveRules =
            $this->resourceRule->getRulesFromProduct($timestamp, $secondWebsiteId, 1, $productId);

        $this->assertCount(1, $mainWebsiteActiveRules);
        // Avoid failure when staging is enabled as it removes catalog rule timestamp.
        if ((int)$mainWebsiteActiveRules[0]['from_time'] !== 0) {
            $this->assertCount(0, $secondWebsiteActiveRules);
        }
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexByIds()
    {
        $this->prepareProducts();

        $this->indexerBuilder->reindexByIds(
            [
                $this->product->getId(),
                $this->productSecond->getId(),
                $this->productThird->getId(),
            ]
        );

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->product->getId()));
        $this->assertEquals(
            9.8,
            $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->productSecond->getId())
        );
        $this->assertFalse($this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->productThird->getId()));
    }

    protected function prepareProducts()
    {
        $product = $this->product->loadByAttribute('sku', 'simple');
        $product->load($product->getId());
        $this->product = $product;

        $this->product->setStoreId(0)->setData('test_attribute', 'test_attribute_value')->save();
        $this->productSecond = clone $this->product;
        $this->productSecond->setId(null)->setUrlKey('product-second')->save();
        $this->productThird = clone $this->product;
        $this->productThird->setId(null)
            ->setUrlKey('product-third')
            ->setData('test_attribute', 'NO_test_attribute_value')
            ->save();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexFull()
    {
        $this->prepareProducts();

        $this->indexerBuilder->reindexFull();

        $rulePrice = $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->product->getId());
        $this->assertEquals(9.8, $rulePrice);
        $rulePrice = $this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->productSecond->getId());
        $this->assertEquals(9.8, $rulePrice);
        $this->assertFalse($this->resourceRule->getRulePrice(new DateTime(), 1, 1, $this->productThird->getId()));
    }

    protected function setUp(): void
    {
        $this->indexerBuilder = Bootstrap::getObjectManager()->get(
            IndexBuilder::class
        );
        $this->resourceRule = Bootstrap::getObjectManager()->get(Rule::class);
        $this->product = Bootstrap::getObjectManager()->get(Product::class);
        $this->storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()
            ->get(Registry::class);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var Collection $productCollection */
        $productCollection = Bootstrap::getObjectManager()->get(
            Collection::class
        );
        $productCollection->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        parent::tearDown();
    }
}
