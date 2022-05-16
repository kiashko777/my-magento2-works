<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Review\Block\Products\ReviewRenderer
 */
class ReviewRendererTest extends TestCase
{
    /**
     * Test verifies ReviewRenderer::getReviewsSummaryHtml call with $displayIfNoReviews = false
     * The reviews summary will be shown as expected only if there is at least one review available
     *
     * @magentoDataFixture Magento/Review/_files/different_reviews.php
     * @magentoAppArea frontend
     */
    public function testGetReviewSummaryHtml()
    {
        $productSku = 'simple';
        $objectManager = Bootstrap::getObjectManager();
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get($productSku);
        /** @var  ReviewRenderer $reviewRenderer */
        $reviewRenderer = $objectManager->create(ReviewRenderer::class);
        $actualResult = $reviewRenderer->getReviewsSummaryHtml($product);
        $this->assertEquals(2, $reviewRenderer->getReviewsCount());
        $this->assertStringContainsString('<span itemprop="reviewCount">2</span>', $actualResult);
    }
}
