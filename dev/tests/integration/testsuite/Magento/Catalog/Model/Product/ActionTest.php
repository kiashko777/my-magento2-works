<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /**
     * @var Action
     */
    private $action;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public static function setUpBeforeClass(): void
    {
        /** @var IndexerRegistry $indexerRegistry */
        $indexerRegistry = Bootstrap::getObjectManager()
            ->get(IndexerRegistry::class);
        $indexerRegistry->get(Fulltext::INDEXER_ID)->setScheduled(true);
    }

    public static function tearDownAfterClass(): void
    {
        /** @var IndexerRegistry $indexerRegistry */
        $indexerRegistry = Bootstrap::getObjectManager()
            ->get(IndexerRegistry::class);
        $indexerRegistry->get(Fulltext::INDEXER_ID)->setScheduled(false);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     */
    public function testUpdateWebsites()
    {
        /** @var WebsiteRepositoryInterface $websiteRepository */
        $websiteRepository = $this->objectManager->create(WebsiteRepositoryInterface::class);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);

        /** @var CacheInterface $cacheManager */
        $pageCache = $this->objectManager->create(\Magento\PageCache\Model\Cache\Type::class);

        /** @var Product $product */
        $product = $productRepository->get('simple');
        foreach ($product->getCategoryIds() as $categoryId) {
            $pageCache->save(
                'test_data',
                'test_data_category_id_' . $categoryId,
                [Category::CACHE_TAG . '_' . $categoryId]
            );
            $this->assertEquals('test_data', $pageCache->load('test_data_category_id_' . $categoryId));
        }

        $websites = $websiteRepository->getList();
        $websiteIds = [];
        foreach ($websites as $websiteCode => $website) {
            if (in_array($websiteCode, ['secondwebsite', 'thirdwebsite'])) {
                $websiteIds[] = $website->getId();
            }
        }

        $this->action->updateWebsites([$product->getId()], $websiteIds, 'add');

        foreach ($product->getCategoryIds() as $categoryId) {
            $this->assertEmpty(
                $pageCache->load('test_data_category_id_' . $categoryId)
            );
        }
    }

    /**
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @magentoAppArea Adminhtml
     * @param string $status
     * @param string $productsCount
     * @dataProvider updateAttributesDataProvider
     * @magentoDbIsolation disabled
     */
    public function testUpdateAttributes($status, $productsCount)
    {
        /** @var IndexerRegistry $indexerRegistry */
        $indexerRegistry = Bootstrap::getObjectManager()
            ->get(IndexerRegistry::class);
        $indexerRegistry->get(Fulltext::INDEXER_ID)->setScheduled(false);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);

        /** @var Product $product */
        $product = $productRepository->get('configurable');
        $productAttributesOptions = $product->getExtensionAttributes()->getConfigurableProductLinks();
        $attrData = ['status' => $status];
        $configurableOptionsId = [];
        if (isset($productAttributesOptions)) {
            foreach ($productAttributesOptions as $configurableOption) {
                $configurableOptionsId[] = $configurableOption;
            }
        }
        $this->action->updateAttributes($configurableOptionsId, $attrData, $product->getStoreId());

        $categoryFactory = $this->objectManager->create(CategoryFactory::class);
        /** @var ListProduct $listProduct */
        $listProduct = $this->objectManager->create(ListProduct::class);
        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();
        $productCollection->joinField(
            'qty',
            'cataloginventory_stock_status',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $this->assertEquals($productsCount, $productCollection->count());
    }

    /**
     * DataProvider for testUpdateAttributes
     *
     * @return array
     */
    public function updateAttributesDataProvider()
    {
        return [
            [
                'status' => 2,
                'expected_count' => 0
            ],
            [
                'status' => 1,
                'expected_count' => 1
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var StateInterface $cacheState */
        $cacheState = $this->objectManager->get(StateInterface::class);
        $cacheState->setEnabled(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER, true);

        $this->action = $this->objectManager->create(Action::class);
    }
}
