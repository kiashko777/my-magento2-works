<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Controller;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class ProductTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testListActionDesign()
    {
        $objectManager = Bootstrap::getObjectManager();
        $product = $objectManager->get(ProductRepositoryInterface::class)
            ->get('custom-design-simple-product');
        $this->getRequest()->setParam('id', $product->getId());
        $this->dispatch('review/product/listAction');
        $result = $this->getResponse()->getBody();
        $this->assertStringNotContainsString("/frontend/Magento/luma/en_US/", $result);
    }
}
