<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase as IndexerTestCase;

/**
 * Class RowTest
 */
class RowTest extends IndexerTestCase
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
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Tests product update
     *
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/row_fixture.php
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     * @magentoAppArea frontend
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function testProductUpdate(): void
    {
        /** @var ListProduct $listProduct */
        $listProduct = $this->objectManager->create(ListProduct::class);

        $this->processor->getIndexer()
            ->setScheduled(false);
        $isScheduled = $this->processor->getIndexer()
            ->isScheduled();
        self::assertFalse(
            $isScheduled,
            'Indexer is in scheduled mode when turned to update on save mode'
        );

        $this->processor->reindexAll();

        $product = $this->productRepository->get('simple');
        $product->setName('Updated Products');
        $this->productRepository->save($product);

        /** @var CategoryInterface $category */
        $category = $this->categoryRepository->get(9);
        /** @var Layer $layer */
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        /** @var Collection $productCollection */
        $productCollection = $layer->getProductCollection();
        self::assertTrue(
            $productCollection->isEnabledFlat(),
            'Products collection is not using flat resource when flat is on'
        );

        self::assertEquals(
            2,
            $productCollection->count(),
            'Products collection items count must be exactly 2'
        );

        foreach ($productCollection as $product) {
            /** @var $product Product */
            if ($product->getSku() === 'simple') {
                self::assertEquals(
                    'Updated Products',
                    $product->getName(),
                    'Products name from flat does not match with updated name'
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->processor = $this->objectManager->get(Processor::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->categoryRepository = $this->objectManager->get(CategoryRepositoryInterface::class);
    }
}
