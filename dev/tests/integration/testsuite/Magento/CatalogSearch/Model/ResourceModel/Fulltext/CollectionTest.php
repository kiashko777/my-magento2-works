<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Model\ResourceModel\Fulltext;

use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\Layer\Search;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection.
 * @magentoDbIsolation disabled
 */
class CollectionTest extends TestCase
{
    /**
     * @dataProvider filtersDataProviderSearch
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     * @magentoDataFixture Magento/CatalogSearch/_files/full_reindex.php
     * @magentoAppIsolation enabled
     */
    public function testLoadWithFilterSearch($request, $filters, $expectedCount)
    {
        $objManager = Bootstrap::getObjectManager();
        /** @var  Collection $fulltextCollection */
        $fulltextCollection = $objManager->create(
            Collection::class,
            ['searchRequestName' => $request]
        );
        foreach ($filters as $field => $value) {
            $fulltextCollection->addFieldToFilter($field, $value);
        }
        if ($request == 'quick_search_container' && isset($filters['search_term'])) {
            $fulltextCollection->addSearchFilter($filters['search_term']);
        }
        $fulltextCollection->loadWithFilter();
        $items = $fulltextCollection->getItems();
        $this->assertCount($expectedCount, $items);
    }

    /**
     * @dataProvider filtersDataProviderQuickSearch
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     * @magentoAppIsolation enabled
     */
    public function testLoadWithFilterQuickSearch($filters, $expectedCount)
    {
        $objManager = Bootstrap::getObjectManager();
        $searchLayer = $objManager->create(Search::class);
        /** @var  Collection $fulltextCollection */
        $fulltextCollection = $searchLayer->getProductCollection();
        foreach ($filters as $field => $value) {
            $fulltextCollection->addFieldToFilter($field, $value);
        }
        if (isset($filters['search_term'])) {
            $fulltextCollection->addSearchFilter($filters['search_term']);
        }
        $fulltextCollection->loadWithFilter();
        $items = $fulltextCollection->getItems();
        $this->assertCount($expectedCount, $items);
    }

    /**
     * @dataProvider filtersDataProviderCatalogView
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     * @magentoAppIsolation enabled
     */
    public function testLoadWithFilterCatalogView($filters, $expectedCount)
    {
        $objManager = Bootstrap::getObjectManager();
        $searchLayer = $objManager->create(Category::class);
        /** @var  Collection $fulltextCollection */
        $fulltextCollection = $searchLayer->getProductCollection();
        foreach ($filters as $field => $value) {
            $fulltextCollection->addFieldToFilter($field, $value);
        }
        $fulltextCollection->loadWithFilter();
        $items = $fulltextCollection->getItems();
        $this->assertCount($expectedCount, $items);
    }

    /**
     * @magentoDataFixture Magento/Framework/Search/_files/products_with_the_same_search_score.php
     * @magentoAppIsolation enabled
     */
    public function testSearchResultsAreTheSameForSameRequests()
    {
        $howManySearchRequests = 3;
        $previousResult = null;

        $objManager = Bootstrap::getObjectManager();

        foreach (range(1, $howManySearchRequests) as $i) {
            $searchLayer = $objManager->create(Search::class);
            /** @var  Collection $fulltextCollection */
            $fulltextCollection = $searchLayer->getProductCollection();

            $fulltextCollection->addSearchFilter('shorts');
            $fulltextCollection->setOrder('relevance');
            $fulltextCollection->load();
            $items = $fulltextCollection->getItems();
            $this->assertGreaterThan(
                0,
                count($items),
                sprintf("Search #%s result must not be empty", $i)
            );

            if ($previousResult) {
                $this->assertEquals(
                    $previousResult,
                    array_keys($items),
                    "Search result must be the same for the same requests"
                );
            }

            $previousResult = array_keys($items);
        }
    }

    public function filtersDataProviderSearch()
    {
        return [
            ['quick_search_container', ['search_term' => '  shorts'], 2],
            ['quick_search_container', ['search_term' => '   '], 0],
            ['catalog_view_container', ['category_ids' => 2], 5],
            ['catalog_view_container', ['category_ids' => 100001], 0],
            ['catalog_view_container', ['category_ids' => []], 0],
            ['catalog_view_container', [], 0],
        ];
    }

    public function filtersDataProviderQuickSearch()
    {
        return [
            [['search_term' => '  shorts'], 2],
            [['search_term' => 'nonexistent'], 0],
        ];
    }

    public function filtersDataProviderCatalogView()
    {
        return [
            [['category_ids' => 2], 5],
            [['category_ids' => 100001], 0],
            [['category_ids' => []], 5],
            [[], 5],
        ];
    }

    /**
     * Test configurable product with multiple options
     *
     * @magentoDataFixture Magento/CatalogSearch/_files/product_configurable_two_options.php
     * @magentoDataFixture Magento/CatalogSearch/_files/full_reindex.php
     * @magentoAppIsolation enabled
     * @dataProvider configurableProductWithMultipleOptionsDataProvider
     * @param array $filters
     * @param bool $found
     * @param array $outOfStock
     * @throws NoSuchEntityException
     */
    public function testConfigurableProductWithMultipleOptions(array $filters, bool $found, array $outOfStock = [])
    {
        $objectManager = Bootstrap::getObjectManager();
        /**@var $stockRegistry StockRegistry */
        $stockRegistry = $objectManager->get(StockRegistry::class);
        /**@var $stockItemRepository StockItemRepositoryInterface */
        $stockItemRepository = $objectManager->get(StockItemRepositoryInterface::class);
        $collection = $objectManager->create(
            Collection::class,
            ['searchRequestName' => 'filter_by_configurable_product_options']
        );
        foreach ($outOfStock as $sku) {
            $stockItem = $stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty(0);
            $stockItem->setIsInStock(0);
            $stockItemRepository->save($stockItem);
        }

        $options = ['test_configurable', 'test_configurable_2'];
        foreach ($options as $option) {
            if (isset($filters[$option])) {
                $filters[$option] = $this->getOptionValue($option, $filters[$option]);
            }
        }
        $filters['category_ids'] = 2;
        foreach ($filters as $field => $value) {
            $collection->addFieldToFilter($field, $value);
        }
        $collection->load();
        $items = $collection->getItems();
        if ($found) {
            $this->assertCount(1, $items);
            $item = array_shift($items);
            $this->assertEquals('configurable_with_2_opts', $item['sku']);
        }
        $this->assertCount(0, $items);
    }

    /**
     * Get attribute option value by label
     *
     * @param string $attributeName
     * @param string $optionLabel
     * @return string|null
     */
    private function getOptionValue(string $attributeName, string $optionLabel): ?string
    {
        $objectManager = Bootstrap::getObjectManager();
        $eavConfig = $objectManager->get(Config::class);
        $attribute = $eavConfig->getAttribute(Product::ENTITY, $attributeName);
        $option = null;
        foreach ($attribute->getOptions() as $option) {
            if ($option->getLabel() === $optionLabel) {
                return $option->getValue();
            }
        }
        return null;
    }

    /**
     * Provide filters to test configurable product with multiple options
     *
     * @return array
     */
    public function configurableProductWithMultipleOptionsDataProvider(): array
    {
        return [
            [
                [],
                true
            ],
            [
                ['test_configurable' => 'Option 1'],
                true
            ],
            [
                ['test_configurable' => 'Option 2'],
                true
            ],
            [
                ['test_configurable_2' => 'Option 1'],
                true
            ],
            [
                ['test_configurable_2' => 'Option 2'],
                true
            ],
            [
                ['test_configurable' => 'Option 1', 'test_configurable_2' => 'Option 1'],
                true
            ],
            [
                ['test_configurable' => 'Option 1', 'test_configurable_2' => 'Option 2'],
                true
            ],
            [
                ['test_configurable' => 'Option 2', 'test_configurable_2' => 'Option 1'],
                true
            ],
            [
                ['test_configurable' => 'Option 2', 'test_configurable_2' => 'Option 2'],
                true
            ],
            [
                ['test_configurable' => 'Option 2', 'test_configurable_2' => 'Option 2'],
                false,
                [
                    'configurable2_option_12',
                    'configurable2_option_22',
                ]
            ],
            [
                ['test_configurable' => 'Option 2', 'test_configurable_2' => 'Option 2'],
                false,
                [
                    'configurable2_option_21',
                    'configurable2_option_22',
                ]
            ],
            [
                ['test_configurable' => 'Option 2'],
                false,
                [
                    'configurable2_option_21',
                    'configurable2_option_22',
                ]
            ],
            [
                [],
                false,
                [
                    'configurable2_option_11',
                    'configurable2_option_12',
                    'configurable2_option_21',
                    'configurable2_option_22',
                ]
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->markTestSkipped("MC-18332: Mysql Search Engine is deprecated and will be removed");
    }
}
