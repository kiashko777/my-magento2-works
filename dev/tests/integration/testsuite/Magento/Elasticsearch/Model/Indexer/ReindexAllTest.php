<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Elasticsearch\Model\Indexer;

use Magento\AdvancedSearch\Model\Client\ClientInterface as ElasticsearchClient;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Elasticsearch\Model\Config;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch\SearchAdapter\SearchIndexNameResolver;
use Magento\Framework\Search\EngineResolverInterface;
use Magento\Indexer\Model\Indexer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleCatalogSearch\Model\ElasticsearchVersionChecker;
use PHPUnit\Framework\TestCase;

/**
 * Important: Please make sure that each integration test file works with unique elastic search index. In order to
 * achieve this, use @magentoConfigFixture to pass unique value for 'elasticsearch_index_prefix' for every test
 * method.
 * E.g. '@magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix indexerhandlertest_configurable'
 *
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReindexAllTest extends TestCase
{
    /**
     * @var string
     */
    private $searchEngine;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var ElasticsearchClient
     */
    private $client;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $clientConfig;

    /**
     * @var SearchIndexNameResolver
     */
    private $searchIndexNameResolver;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Test search of all products after full reindex
     *
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix indexerhandlertest_configurable
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
     */
    public function testSearchAll()
    {
        $this->reindexAll();
        $result = $this->searchByName('Configurable Products');
        self::assertGreaterThanOrEqual(2, $result);
    }

    /**
     * Make fulltext catalog search reindex
     *
     * @return void
     */
    private function reindexAll()
    {
        // Perform full reindex
        /** @var Indexer $indexer */
        $indexer = Bootstrap::getObjectManager()->create(Indexer::class);
        $indexer->load('catalogsearch_fulltext');
        $indexer->reindexAll();
    }

    /**
     * @param string $text
     * @return array
     */
    private function searchByName($text)
    {
        $storeId = $this->storeManager->getDefaultStoreView()->getId();
        $searchQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, 'catalogsearch_fulltext'),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'query' => [
                    'bool' => [
                        'minimum_should_match' => 1,
                        'should' => [
                            [
                                'match' => [
                                    'name' => $text,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $queryResult = $this->client->query($searchQuery);
        return isset($queryResult['hits']['hits']) ? $queryResult['hits']['hits'] : [];
    }

    /**
     * Test sorting of all products after full reindex
     *
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix indexerhandlertest_configurable
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
     */
    public function testSort()
    {
        /** @var $productFifth Product */
        $productSimple = Bootstrap::getObjectManager()->create(Product::class);
        $productSimple->setTypeId('simple')
            ->setAttributeSetId(4)
            ->setWebsiteIds([1])
            ->setName('ABC')
            ->setSku('abc-first-in-sort')
            ->setPrice(20)
            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setStockData(['use_config_manage_stock' => 0])
            ->save();
        $productConfigurableOption = $this->productRepository->get('simple_10');
        $productConfigurableOption->setName('1ABC');
        $this->productRepository->save($productConfigurableOption);
        $this->reindexAll();
        $productSimple = $this->productRepository->get('abc-first-in-sort');
        $result = $this->sortByName();
        $firstInSearchResults = (int)$result[0]['_id'];
        $productSimpleId = (int)$productSimple->getId();
        $this->assertEquals($productSimpleId, $firstInSearchResults);
    }

    /**
     * @return array
     */
    private function sortByName()
    {
        $storeId = $this->storeManager->getDefaultStoreView()->getId();
        $searchQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, 'catalogsearch_fulltext'),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'sort' => [
                    'name.sort_name' => [
                        'order' => 'asc'
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'terms' => [
                                    'visibility' => [2, 4],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $queryResult = $this->client->query($searchQuery);
        return isset($queryResult['hits']['hits']) ? $queryResult['hits']['hits'] : [];
    }

    /**
     * Test sorting of products with lower and upper case names after full reindex
     *
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix indexerhandlertest
     * @magentoDataFixture Magento/Elasticsearch/_files/case_sensitive.php
     */
    public function testSortCaseSensitive(): void
    {
        $productFirst = $this->productRepository->get('fulltext-1');
        $productSecond = $this->productRepository->get('fulltext-2');
        $productThird = $this->productRepository->get('fulltext-3');
        $productFourth = $this->productRepository->get('fulltext-4');
        $productFifth = $this->productRepository->get('fulltext-5');
        $correctSortedIds = [
            $productFirst->getId(),
            $productFourth->getId(),
            $productSecond->getId(),
            $productFifth->getId(),
            $productThird->getId(),
        ];
        $this->reindexAll();
        $result = $this->sortByName();
        $firstInSearchResults = (int)$result[0]['_id'];
        $secondInSearchResults = (int)$result[1]['_id'];
        $thirdInSearchResults = (int)$result[2]['_id'];
        $fourthInSearchResults = (int)$result[3]['_id'];
        $fifthInSearchResults = (int)$result[4]['_id'];
        $actualSortedIds = [
            $firstInSearchResults,
            $secondInSearchResults,
            $thirdInSearchResults,
            $fourthInSearchResults,
            $fifthInSearchResults
        ];
        $this->assertCount(5, $result);
        $this->assertEquals($correctSortedIds, $actualSortedIds);
    }

    /**
     * Test search of specific product after full reindex
     *
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix indexerhandlertest_configurable
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
     */
    public function testSearchSpecificProduct()
    {
        $this->reindexAll();
        $result = $this->searchByName('12345');
        self::assertCount(1, $result);

        $specificProduct = $this->productRepository->get('configurable_12345');
        self::assertEquals($specificProduct->getId(), $result[0]['_id']);
    }

    protected function setUp(): void
    {
        $this->connectionManager = Bootstrap::getObjectManager()->create(ConnectionManager::class);
        $this->client = $this->connectionManager->getConnection();
        $this->storeManager = Bootstrap::getObjectManager()->create(StoreManagerInterface::class);
        $this->clientConfig = Bootstrap::getObjectManager()->create(Config::class);
        $this->searchIndexNameResolver = Bootstrap::getObjectManager()->create(SearchIndexNameResolver::class);
        $this->productRepository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);
    }

    /**
     * Make sure that correct engine is set
     */
    protected function assertPreConditions(): void
    {
        $currentEngine = Bootstrap::getObjectManager()->get(EngineResolverInterface::class)->getCurrentSearchEngine();
        $this->assertEquals($this->getInstalledSearchEngine(), $currentEngine);
    }

    /**
     * Returns installed on server search service
     *
     * @return string
     */
    private function getInstalledSearchEngine()
    {
        if (!$this->searchEngine) {
            // phpstan:ignore "Class Magento\TestModuleCatalogSearch\Model\ElasticsearchVersionChecker not found."
            $version = Bootstrap::getObjectManager()->get(ElasticsearchVersionChecker::class)->getVersion();
            $this->searchEngine = 'elasticsearch' . $version;
        }
        return $this->searchEngine;
    }
}
