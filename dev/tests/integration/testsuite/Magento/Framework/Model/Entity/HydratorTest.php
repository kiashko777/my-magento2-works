<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\Entity;

use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\EntityManager\Hydrator;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    const CUSTOM_ATTRIBUTE_CODE = 'description';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testExtractAndHydrate()
    {
        /** @var Hydrator $hydrator */
        $hydrator = $this->objectManager->create(Hydrator::class);

        /** @var AttributeInterface $customAttribute */
        $customAttribute = $this->objectManager->create(AttributeInterface::class);
        $customAttribute->setAttributeCode(self::CUSTOM_ATTRIBUTE_CODE)
            ->setValue('Products description');

        /** @var StockItemInterface $extensionAttribute */
        $stockItem = $this->objectManager->create(
            StockItemInterface::class
        );
        $stockItem->setProductId(1)
            ->setQty(100);

        /** @var ProductExtension $productExtension */
        $productExtension = $this->objectManager->create(ProductExtension::class);
        $productExtension->setStockItem($stockItem);

        /** @var ProductLinkInterface $productLink */
        $productLink = $this->objectManager->create(ProductLinkInterface::class);
        $productLink->setSku('sku')
            ->setLinkedProductSku('linked-sku');

        /** @var ProductInterface $product */
        $product = $this->objectManager->create(ProductInterface::class);
        $product->setSku('sku')
            ->setName('Products name')
            ->setCustomAttributes([self::CUSTOM_ATTRIBUTE_CODE => $customAttribute])
            ->setExtensionAttributes($productExtension)
            ->setProductLinks([$productLink]);

        $productData = $hydrator->extract($product);
        /** @var ProductInterface $newProduct */
        $newProduct = $this->objectManager->create(ProductInterface::class);
        $newProduct = $hydrator->hydrate($newProduct, $productData);
        $newProductData = $hydrator->extract($newProduct);

        $this->assertEquals($productData, $newProductData);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
