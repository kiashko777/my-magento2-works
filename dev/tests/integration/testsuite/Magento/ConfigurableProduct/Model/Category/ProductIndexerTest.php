<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Indexer\Model\Indexer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @magentoDataFixture Magento/Catalog/_files/indexer_catalog_category.php
 * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
 * @magentoAppIsolation enabled
 * @magentoDbIsolation disabled
 */
class ProductIndexerTest extends TestCase
{
    const DEFAULT_ROOT_CATEGORY = 2;

    /**
     * @var IndexerInterface
     */
    private $indexer;

    /**
     * @var Product
     */
    private $productResource;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @magentoAppArea Adminhtml
     */
    public function testCategoryMove()
    {
        $categories = $this->getCategories();

        /** @var Category $categoryFourth */
        $categoryFourth = end($categories);
        $configurableProduct = $this->productRepository->get('configurable');
        $configurableProduct->setCategoryIds([$categoryFourth->getId()]);
        $this->productRepository->save($configurableProduct);

        /** @var Category $categorySecond */
        $categorySecond = $categories[1];
        $categorySecond->setIsAnchor(true);
        $this->categoryRepository->save($categorySecond);

        /** @var Category $categoryThird */
        $categoryThird = $categories[2];
        $categoryThird->setIsAnchor(true);
        $this->categoryRepository->save($categoryThird);

        $this->indexer->reindexAll();

        /**
         * Move category from $categoryThird to $categorySecond
         */
        $categoryFourth->move($categorySecond->getId(), null);

        $categories = [self::DEFAULT_ROOT_CATEGORY, $categorySecond->getId(), $categoryFourth->getId()];

        foreach ($categories as $categoryId) {
            $this->assertTrue((bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryId));
        }

        $this->assertFalse(
            (bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryThird->getId())
        );
    }

    /**
     * @return Category[]
     */
    private function getCategories()
    {
        /** @var Category $category */
        $category = Bootstrap::getObjectManager()->create(
            Category::class
        );

        $result = $category->getCollection()->addAttributeToSelect('name')->getItems();
        $result = array_slice($result, 2);

        return array_slice($result, 0, 4);
    }

    /**
     * @magentoAppArea Adminhtml
     * @depends testReindex
     */
    public function testCategoryDelete()
    {
        $categories = $this->getCategories();
        $configurableProduct = $this->productRepository->get('configurable');

        /** @var Category $categoryFourth */
        $categoryFourth = end($categories);
        $this->categoryRepository->delete($categoryFourth);

        /** @var Category $categorySecond */
        $categorySecond = $categories[1];

        $categories = [$categorySecond->getId(), $categoryFourth->getId()];

        foreach ($categories as $categoryId) {
            $this->assertFalse((bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryId));
        }
        $this->assertTrue(
            (bool)$this->productResource->canBeShowInCategory(
                $configurableProduct,
                self::DEFAULT_ROOT_CATEGORY
            )
        );
    }

    /**
     * @magentoAppArea Adminhtml
     */
    public function testCategoryCreate()
    {
        $this->testReindex();
        $categories = $this->getCategories();
        $configurableProduct = $this->productRepository->get('configurable');

        /** @var Category $categorySecond */
        $categorySecond = $categories[1];
        $categorySecond->setIsAnchor(0);
        $this->categoryRepository->save($categorySecond);

        /** @var Category $categoryFourth */
        $categoryFourth = end($categories);

        /** @var Category $categorySixth */
        $categorySixth = Bootstrap::getObjectManager()->create(
            Category::class
        );
        $categorySixth->setName(
            'Category 6'
        )->setPath(
            $categoryFourth->getPath()
        )->setAvailableSortBy(
            'name'
        )->setDefaultSortBy(
            'name'
        )->setIsActive(
            true
        );
        $this->categoryRepository->save($categorySixth);

        $configurableProduct->setCategoryIds([$categorySixth->getId()]);
        $configurableProduct->save();

        $categories = [self::DEFAULT_ROOT_CATEGORY, $categorySixth->getId(), $categoryFourth->getId()];
        foreach ($categories as $categoryId) {
            $this->assertTrue((bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryId));
        }

        $this->assertFalse(
            (bool)$this->productResource->canBeShowInCategory($configurableProduct, $categorySecond->getId())
        );
    }

    /**
     * @magentoAppArea Adminhtml
     */
    public function testReindex()
    {
        $categories = $this->getCategories();

        /** @var Category $categoryFourth */
        $categoryFourth = end($categories);
        /** @var \Magento\Catalog\Model\Product $configurableProduct */
        $configurableProduct = $this->productRepository->get('configurable');
        $configurableProduct->setCategoryIds([$categoryFourth->getId()]);
        $this->productRepository->save($configurableProduct);

        /** @var Category $categoryThird */
        $categoryThird = $categories[2];
        $categoryThird->setIsAnchor(true);
        $this->categoryRepository->save($categoryThird);

        $this->indexer->reindexAll();

        $categories = [self::DEFAULT_ROOT_CATEGORY, $categoryThird->getId(), $categoryFourth->getId()];
        foreach ($categories as $categoryId) {
            $this->assertTrue((bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryId));
        }

        $this->assertTrue(
            (bool)$this->productResource->canBeShowInCategory($configurableProduct, $categoryThird->getParentId())
        );
    }

    protected function setUp(): void
    {
        $this->indexer = Bootstrap::getObjectManager()->create(
            Indexer::class
        );
        $this->indexer->load('catalog_category_product');

        $this->productResource = Bootstrap::getObjectManager()->get(
            Product::class
        );
        $this->productRepository = Bootstrap::getObjectManager()->get(
            ProductRepository::class
        );
        $this->categoryRepository = Bootstrap::getObjectManager()->get(
            CategoryRepository::class
        );
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $reflection = new ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            if (!$property->isStatic() && 0 !== strpos($property->getDeclaringClass()->getName(), 'PHPUnit')) {
                $property->setAccessible(true);
                $property->setValue($this, null);
            }
        }
    }
}
