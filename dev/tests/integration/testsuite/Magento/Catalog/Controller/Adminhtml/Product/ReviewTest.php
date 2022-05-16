<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\Review;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class ReviewTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Review/_files/review_xss.php
     */
    public function testEditActionProductNameXss()
    {
        $objectManager = Bootstrap::getObjectManager();
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('product-with-xss');

        $reviewId = Bootstrap::getObjectManager()->create(
            Review::class
        )->load(
            $product->getId(),
            'entity_pk_value'
        )->getId();
        $this->dispatch('backend/review/product/edit/id/' . $reviewId);
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $responseBody);
        $this->assertStringNotContainsString('<script>alert("xss");</script>', $responseBody);
    }
}
