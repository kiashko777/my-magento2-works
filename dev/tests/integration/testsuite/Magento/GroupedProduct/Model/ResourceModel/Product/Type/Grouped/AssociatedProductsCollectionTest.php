<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Model\ResourceModel\Product\Type\Grouped;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class AssociatedProductsCollectionTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/GroupedProduct/_files/product_grouped.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testGetColumnValues()
    {
        $productRepository = Bootstrap::getObjectManager()
            ->get(ProductRepositoryInterface::class);
        /** @var $product Product */
        $product = $productRepository->get('grouped-product');

        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_product', $product);

        $collection = Bootstrap::getObjectManager()->create(
            AssociatedProductsCollection::class
        );

        $resultData = $collection->getColumnValues('sku');
        $this->assertNotEmpty($resultData);

        $expected = ['virtual-product', 'simple'];
        sort($expected);
        sort($resultData);
        $this->assertEquals($expected, $resultData);
    }
}
