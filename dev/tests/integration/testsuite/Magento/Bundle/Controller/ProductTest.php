<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Catalog\Controller\Products (bundle product type)
 */

namespace Magento\Bundle\Controller;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractController;

class ProductTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @magentoDbIsolation disabled
     */
    public function testViewAction()
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('bundle-product');
        $this->dispatch('catalog/product/view/id/' . $product->getEntityId());
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('Bundle Products', $responseBody);
        $this->assertStringContainsString(
            'In stock',
            $responseBody,
            'Bundle Products Detailed Page does not contain In Stock field'
        );
        $addToCartCount = substr_count($responseBody, '<span>Add to Cart</span>');
        $this->assertEquals(1, $addToCartCount, '"Add to Cart" button should appear on the page exactly once.');
        $actualLinkCount = substr_count($responseBody, '>Bundle Products Items<');
        $this->assertEquals(1, $actualLinkCount, 'Bundle product options should appear on the page exactly once.');
        $this->assertStringNotContainsString('class="options-container-big"', $responseBody);
        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//*[@id="product-options-wrapper"]',
                $responseBody
            )
        );
    }
}
