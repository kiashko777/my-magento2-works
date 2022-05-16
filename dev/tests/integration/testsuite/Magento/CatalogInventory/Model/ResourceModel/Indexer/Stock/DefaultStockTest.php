<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockStatusCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class DefaultStockTest
 * @magentoAppArea Adminhtml
 */
class DefaultStockTest extends TestCase
{
    /**
     * @var DefaultStock
     */
    private $indexer;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @magentoDataFixture Magento/Store/_files/website.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     *
     * @magentoDbIsolation disabled
     *
     * @throws NoSuchEntityException
     */
    public function testReindexEntity()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->getObject(ProductRepository::class);
        /** @var WebsiteRepositoryInterface $websiteRepository */
        $websiteRepository = $this->getObject(
            WebsiteRepositoryInterface::class
        );
        $product = $productRepository->get('simple');
        $testWebsite = $websiteRepository->get('test');
        $product->setWebsiteIds([1, $testWebsite->getId()])->save();

        /** @var StockStatusCriteriaInterfaceFactory $criteriaFactory */
        $criteriaFactory = $this->getObject(
            StockStatusCriteriaInterfaceFactory::class
        );
        /** @var StockStatusRepositoryInterface $stockStatusRepository */
        $stockStatusRepository = $this->getObject(
            StockStatusRepositoryInterface::class
        );
        $criteria = $criteriaFactory->create();
        $criteria->setProductsFilter([$product->getId()]);
        $criteria->addFilter('website', 'website_id', $this->stockConfiguration->getDefaultScopeId());
        $items = $stockStatusRepository->getList($criteria)->getItems();
        $this->assertEquals($product->getId(), $items[$product->getId()]->getProductId());
    }

    private function getObject($class)
    {
        return Bootstrap::getObjectManager()->create(
            $class
        );
    }

    protected function setUp(): void
    {
        $this->indexer = Bootstrap::getObjectManager()->get(
            DefaultStock::class
        );
        $this->stockConfiguration = Bootstrap::getObjectManager()->get(
            StockConfigurationInterface::class
        );
    }
}
