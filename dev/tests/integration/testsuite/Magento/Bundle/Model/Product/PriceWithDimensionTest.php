<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Bundle\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation disabled
 * @magentoIndexerDimensionMode catalog_product_price website_and_customer_group
 * @group indexer_dimension
 * @magentoDataFixture Magento/Bundle/_files/product_with_tier_pricing.php
 */
class PriceWithDimensionTest extends TestCase
{
    /**
     * @var Price
     */
    protected $_model;

    /**
     * Get tier price
     */
    public function testGetTierPrice()
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = Bootstrap::getObjectManager()
            ->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('bundle-product');
        // fixture

        // Note that this is really not the "tier price" but the "tier discount percentage"
        // so it is expected to be increasing instead of decreasing
        $this->assertEquals(8.0, $this->_model->getTierPrice(2, $product));
        $this->assertEquals(20.0, $this->_model->getTierPrice(3, $product));
        $this->assertEquals(20.0, $this->_model->getTierPrice(4, $product));
        $this->assertEquals(30.0, $this->_model->getTierPrice(5, $product));
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Price::class
        );
    }
}
