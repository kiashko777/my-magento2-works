<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model\Indexer;

use DateTime;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/CatalogRule/_files/two_rules.php
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class BatchIndexTest extends TestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Rule
     */
    protected $resourceRule;

    /**
     * @magentoDbIsolation disabled
     * @dataProvider dataProvider
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/two_rules.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testPriceForSmallBatch($batchCount, $price, $expectedPrice)
    {
        $productIds = $this->prepareProducts($price);

        /**
         * @var IndexBuilder $indexerBuilder
         */
        $indexerBuilder = Bootstrap::getObjectManager()->create(
            IndexBuilder::class,
            ['batchCount' => $batchCount]
        );

        $indexerBuilder->reindexFull();

        foreach ([0, 1] as $customerGroupId) {
            foreach ($productIds as $productId) {
                $this->assertEquals(
                    $expectedPrice,
                    $this->resourceRule->getRulePrice(new DateTime(), 1, $customerGroupId, $productId)
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareProducts($price)
    {
        $this->product = $this->productRepository->get('simple');
        $productSecond = clone $this->product;
        $productSecond->setId(null)
            ->setUrlKey(null)
            ->setSku(uniqid($this->product->getSku() . '-'))
            ->setName(uniqid($this->product->getName() . '-'))
            ->setWebsiteIds([1])
            ->save();
        $productSecond->setPrice($price);
        $this->productRepository->save($productSecond);
        $productThird = clone $this->product;
        $productThird->setId(null)
            ->setUrlKey(null)
            ->setSku(uniqid($this->product->getSku() . '--'))
            ->setName(uniqid($this->product->getName() . '--'))
            ->setWebsiteIds([1])
            ->save();
        $productThird->setPrice($price);
        $this->productRepository->save($productThird);

        return [
            $productSecond->getEntityId(),
            $productThird->getEntityId(),
        ];
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [1, 20, 17],
            [3, 40, 36],
            [3, 60, 55],
            [5, 100, 93],
            [8, 200, 188],
            [10, 500, 473],
            [11, 760, 720],
        ];
    }

    protected function setUp(): void
    {
        $this->resourceRule = Bootstrap::getObjectManager()->get(Rule::class);
        $this->product = Bootstrap::getObjectManager()->get(Product::class);
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepository::class);
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
