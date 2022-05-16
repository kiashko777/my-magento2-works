<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor;
use Magento\Catalog\Model\ResourceModel\Product\Flat;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase as IndexerTestCase;

/**
 * Custom Flat Attribute Test
 */
class CustomFlatAttributeTest extends IndexerTestCase
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Tests that custom product attribute will appear in flat table and can be updated in it.
     *
     * @magentoDbIsolation disabled
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple_with_custom_attribute_in_flat.php
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function testProductUpdateCustomAttribute(): void
    {
        $product = $this->productRepository->get('simple_with_custom_flat_attribute');
        $product->setCustomAttribute('flat_attribute', 'changed flat attribute');
        $this->productRepository->save($product);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        /** @var SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'simple_with_custom_flat_attribute')
            ->create();

        $items = $this->productRepository->getList($searchCriteria)
            ->getItems();
        $product = reset($items);
        $resourceModel = $product->getResourceCollection()
            ->getEntity();

        self::assertInstanceOf(
            Flat::class,
            $resourceModel,
            'Products should be received from flat resource'
        );

        self::assertEquals(
            'changed flat attribute',
            $product->getFlatAttribute(),
            'Products flat attribute should be able to change.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->processor = $this->objectManager->get(Processor::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
    }
}
