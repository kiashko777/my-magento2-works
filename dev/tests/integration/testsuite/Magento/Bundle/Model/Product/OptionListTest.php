<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for Magento\Bundle\Model\OptionList
 */
class OptionListTest extends TestCase
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @magentoDbIsolation disabled
     */
    public function testGetItems()
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $this->product = $productRepository->get('bundle-product');
        /**
         * @var OptionList $optionList
         */
        $optionList = $this->objectManager->create(OptionList::class);
        $options = $optionList->getItems($this->product);
        $this->assertCount(1, $options);
        $this->assertEquals('Bundle Products Items', $options[0]->getTitle());
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
