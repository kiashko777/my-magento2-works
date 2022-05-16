<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Bundle\Model\Products\Type (bundle product type)
 */
class TypeTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Full reindex
     *
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Connection adapter
     *
     * @var AdapterInterface
     */
    protected $connectionMock;

    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @covers \Magento\Bundle\Model\Product\Type::getSearchableData
     * @magentoDbIsolation disabled
     */
    public function testGetSearchableData()
    {
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        /** @var Product $bundleProduct */
        $bundleProduct = $productRepository->get('bundle-product');
        $bundleType = $bundleProduct->getTypeInstance();
        /** @var Type $bundleType */
        $searchableData = $bundleType->getSearchableData($bundleProduct);

        $this->assertCount(1, $searchableData);
        $this->assertEquals('Bundle Products Items', $searchableData[0]);
    }

    /**
     * @magentoDataFixture Magento/Bundle/_files/product_with_multiple_options.php
     * @covers \Magento\Bundle\Model\Product\Type::getOptionsCollection
     * @magentoDbIsolation disabled
     */
    public function testGetOptionsCollection()
    {
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        /** @var Product $bundleProduct */
        $bundleProduct = $productRepository->get('bundle-product');
        $bundleType = $bundleProduct->getTypeInstance();
        /** @var Type $bundleType */
        $options = $bundleType->getOptionsCollection($bundleProduct);
        $this->assertCount(5, $options->getItems());
    }

    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @covers \Magento\Bundle\Model\Product\Type::getParentIdsByChild()
     * @magentoDbIsolation disabled
     */
    public function testGetParentIdsByChild()
    {
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        /** @var ProductInterface $bundleProduct */
        $bundleProduct = $productRepository->get('bundle-product');
        /** @var ProductInterface $simpleProduct */
        $simpleProduct = $productRepository->get('simple');

        /** @var Type $bundleType */
        $bundleType = $bundleProduct->getTypeInstance();
        $parentIds = $bundleType->getParentIdsByChild($simpleProduct->getId());
        $this->assertNotEmpty($parentIds);
        $this->assertEquals($bundleProduct->getId(), $parentIds[0]);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var IndexerRegistry $indexerRegistry */
        $indexerRegistry = $this->objectManager->create(IndexerRegistry::class);
        $this->indexer = $indexerRegistry->get('catalogsearch_fulltext');

        $this->resource = $this->objectManager->get(ResourceConnection::class);
        $this->connectionMock = $this->resource->getConnection();
    }
}
