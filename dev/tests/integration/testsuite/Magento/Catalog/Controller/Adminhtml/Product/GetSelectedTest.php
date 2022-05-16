<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class GetSelectedTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecute(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $productRepository = $objectManager->get(ProductRepositoryInterface::class);

        $product = $productRepository->get('simple');
        $this->getRequest()
            ->setPostValue('productId', $product->getId());
        $this->dispatch('backend/catalog/product/getSelected');
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString(
            '{"value":"1","label":"Simple Products","is_active":1,"path":"simple"}',
            $responseBody
        );
    }

    public function testExecuteNonExistingSearchKey(): void
    {
        $this->getRequest()
            ->setPostValue('productId', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $this->dispatch('backend/catalog/product/getSelected');
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('[]', $responseBody);
    }
}
